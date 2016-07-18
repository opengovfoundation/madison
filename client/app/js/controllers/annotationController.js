angular.module('madisonApp.controllers')
  .controller('AnnotationController', ['$scope', '$sce', '$http',
    'annotationService', 'loginPopupService', 'growl', '$location',
    '$filter', '$timeout',

    function ($scope, $sce, $http, annotationService, loginPopupService, growl,
      $location, $filter, $timeout) {

      $scope.annotations = [];
      $scope.annotationGroups = [];
      $scope.supported = null;
      $scope.opposed = false;
      $scope.annotationsShow = false;
      $scope.currentGroup = null;

      $timeout(function() {
        $('.annotation-help').tooltip({});
      }, 1000);

      //Parse sub-comment hash if there is one
      var hash = $location.hash();

      var annotationId = hash.match(ANNOTATION_HASH_REGEX);
      var subCommentId = hash.match(ANNOTATION_COMMENT_HASH_REGEX);


      if (annotationId || subCommentId) {
        if (annotationId) $scope.annotationId = parseInt(annotationId[1]);
        if (subCommentId) $scope.subCommentId = parseInt(subCommentId[1]);

        $scope.$on('annotationsSet', function () {
          var parentAnnotationId = annotationId ?
            parseInt(annotationId[1]) : parentAnnotationIdFromComment(parseInt(subCommentId[1]));

          openPanelForAnnotationId(parentAnnotationId);
          if ($scope.subCommentId) scrollToAnnotationComment($scope.subCommentId);
        });
      }

      function openPanelForAnnotationId(id) {
        var group = findGroupFromAnnotationId(id);

        if (!group) return;

        $scope.showNotes(group);
        $(document).scrollTop($('#annotation_' + id).offset().top - 50);
      }

      function scrollToAnnotationComment(commentId) {
        var subCommentHash = 'annsubcomment_' + commentId;

        // Have to wait for sidebar animation to complete before scrolling
        $timeout(function() {
          // Get difference of y position of annotation list and y position
          // of annotation comment to know how much to scroll annotation window
          var top = $('#' + subCommentHash).offset().top;
          var annotationListTop = $('.annotation-list').offset().top;
          $('.annotation-list').scrollTop(top - annotationListTop - 20);
        }, 1500);
      }

      function parentAnnotationIdFromComment(commentId) {
        var foundId;

        angular.forEach($scope.annotations, function (annotation) {
          var commentIds = annotation.comments.map(function (comment) {
            return comment.id;
          });
          if (commentIds.indexOf(commentId) !== -1) foundId = annotation.id;
        });

        return foundId;
      }

      function findGroupFromAnnotationId(annotationId) {
        var foundGroup;

        angular.forEach($scope.annotationGroups, function (group) {
          var idsInGroup = group.annotations.map(function (annotation)  {
            return annotation.id;
          });

          if (idsInGroup.indexOf(annotationId) !== -1) foundGroup = group;
        });

        return foundGroup;
      }

      //Watch for annotationsUpdated broadcast
      var loadTimeout;
      $scope.$on('annotationsUpdated', function () {
        loadAnnotations();
      });

      function loadAnnotations() {
        if(loadTimeout) {
          clearTimeout(loadTimeout);
        }

        var firstAnnotation;
        if(annotationService.annotationGroups) {
          var groups = annotationService.annotationGroups;
          firstAnnotation = groups[Object.keys(groups)[0]];
        }

        // If our annotations are ready show them.
        if(
          firstAnnotation &&
          firstAnnotation.parent &&
          firstAnnotation.parent.offset()
        ) {
          angular.forEach(annotationService.annotations, function (annotation) {
            if ($.inArray(annotation, $scope.annotations) < 0) {
              var collapsed = true;
              if ($scope.subCommentId) {
                angular.forEach(annotation.comments, function (subcomment) {
                  if (subcomment.id == $scope.subCommentId) {
                    collapsed = false;
                  }
                });
              }

              annotation.permalinkBase = 'annotation';
              annotation.label = 'annotation';
              annotation.commentsCollapsed = collapsed;
              $scope.annotations.push(annotation);
            }
          });

          for(var index in annotationService.annotationGroups) {
            var annotationGroup = annotationService.annotationGroups[index];

            //Calculate our offset from the top of the window
            //Skip this group if there is no parent
            if(annotationGroup.parent.length === 0) {
              continue;
            }
            var parentTop = annotationGroup.parent.offset().top;
            var containerTop = $('.annotation-container').offset().top;
            annotationGroup.top = (parentTop - containerTop) + 'px';

            annotationGroup.users = [];
            //Count the unique users for our annotations.
            for(var annotationIndex in annotationGroup.annotations) {
              var annotation = annotationGroup.annotations[annotationIndex];
              if(annotationGroup.users.indexOf(annotation.user.id) < 0) {
                annotationGroup.users.push(annotation.user.id);
              }

              //Then count the unique users for the responses to each annotation.
              for(var commentIndex in annotation.comments) {
                var comment = annotation.comments[commentIndex];
                annotation.comments[commentIndex].permalinkBase = 'annsubcomment';
                annotation.comments[commentIndex].label = 'comment';
                if(annotationGroup.users.indexOf(comment.user.id) < 0) {
                  annotationGroup.users.push(comment.user.id);
                }
              }
            }

            $scope.annotationGroups.push(annotationGroup);
          }

          $scope.$apply();
        }
        // If annotations are not ready, wait half a second and try again.
        else {
          loadTimeout = setTimeout(loadAnnotations, 500);
        }
      }

      $scope.isSponsor = function () {
        var currentId = $scope.user.id;
        var sponsored = false;
        // angular.forEach($scope.doc.sponsor, function (sponsor) {
        //   if (currentId === sponsor.id) {
        //     sponsored = true;
        //   }
        // });

        return sponsored;
      };

      $scope.notifyAuthor = function (annotation) {

        $http.post('/api/docs/' + doc.id + '/comments/' + annotation.id + '/' + 'seen')
          .success(function (data) {
            annotation.seen = data.seen;
          }).error(function (data) {
            console.error("Unable to mark activity as seen: %o", data);
          });
      };

      $scope.activityOrder = function (activity) {
        // Leaving this function in case we want to implement a more complex
        // ordering algorithm in the future
        return activity.likes;
      };

      $scope.collapseComments = function (activity) {
        activity.commentsCollapsed = !activity.commentsCollapsed;
      };

      $scope.showCommentForm = function($event)
      {
        if ($scope.user && $scope.user.id !== '') {
          $('#comment-form-field').focus();
        } else {
          loginPopupService.showLoginForm($event);
        }
      };

      // TODO: duplicate in `commentController`, move somewhere shared
      $scope.subcommentSubmit = function (activity, subcomment) {
        subcomment.user = $scope.user;

        $.post('/api/docs/' + $scope.doc.id + '/comments/' + activity.id + '/comments', subcomment)
        .success(function (data) {
          data.permalinkBase = 'annsubcomment';
          data.label = 'comment';
          activity.comments.push(data);
          subcomment.text = '';
          subcomment.user = '';
          $scope.$apply();
        }).error(function (data) {
          console.error(data);
        });
      };

      $scope.showNotes = function(annotationGroup) {
        $scope.annotationsShow = true;
        $scope.currentGroup = annotationGroup;
      };

      $scope.hideNotes = function() {
        $scope.annotationsShow = false;
        $scope.currentGroup = null;
      };
    }
    ]);
