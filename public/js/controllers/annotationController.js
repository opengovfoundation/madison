angular.module('madisonApp.controllers')
  .controller('AnnotationController', ['$scope', '$sce', '$http',
    'annotationService', 'loginPopupService', 'growl', '$location',
    '$filter', '$timeout', '$anchorScroll',

    function ($scope, $sce, $http, annotationService, loginPopupService, growl,
      $location, $filter, $timeout, $anchorScroll) {

      $scope.annotations = [];
      $scope.annotationGroups = [];
      $scope.supported = null;
      $scope.opposed = false;
      $scope.annotationsShow = false;
      $scope.currentGroup = null;

      //Parse sub-comment hash if there is one
      var hash = $location.hash();

      var annotationId = hash.match(ANNOTATION_HASH_REGEX);
      var subCommentId = hash.match(ANNOTATION_COMMENT_HASH_REGEX);


      if (annotationId || subCommentId) {
        if (subCommentId) {
          $scope.subCommentId = parseInt(subCommentId[1]);
        }

        $scope.$on('annotationsSet', function () {
          var parentAnnotationId = annotationId ?
            parseInt(annotationId[1]) : parentAnnotationIdFromComment(parseInt(subCommentId[1]));

          openPanelForAnnotationId(parentAnnotationId);
        });
      }

      function openPanelForAnnotationId(id) {
        var group = findGroupFromAnnotationId(id);

        if (!group) return;

        $scope.showAnnotations(group);
        $location.hash('annotation_' + id);
        $anchorScroll();
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

              annotation.label = 'annotation';
              annotation.commentsCollapsed = collapsed;
              $scope.annotations.push(annotation);
            }
          });

          for(var index in annotationService.annotationGroups) {
            var annotationGroup = annotationService.annotationGroups[index];

            //Calculate our offset from the top of the window
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
                annotation.comments[commentIndex].label = 'comment';
                annotation.comments[commentIndex].doc_id = annotation.doc_id;
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
        //   console.log(sponsor);
        //   if (currentId === sponsor.id) {
        //     sponsored = true;
        //   }
        // });

        return sponsored;
      };

      $scope.notifyAuthor = function (annotation) {

        $http.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/' + 'seen')
          .success(function (data) {
            annotation.seen = data.seen;
          }).error(function (data) {
            console.error("Unable to mark activity as seen: %o", data);
          });
      };


      $scope.getDocComments = function (docId) {
        $http({
          method: 'GET',
          url: '/api/docs/' + docId + '/comments'
        })
          .success(function (data) {
            angular.forEach(data, function (comment) {
              var collapsed = false;
              if ($scope.subCommentId) {
                angular.forEach(comment.comments, function (subcomment) {
                  if (subcomment.id == $scope.subCommentId) {
                    collapsed = false;
                    subcomment.label = 'comment';
                  }
                });
              }
              comment.commentsCollapsed = collapsed;
              comment.label = 'comment';
              comment.link = 'comment_' + comment.id;
              $scope.annotations.push(comment);
            });
          })
          .error(function (data) {
            console.error("Error loading comments: %o", data);
          });
      };

      $scope.commentSubmit = function () {

        var comment = angular.copy($scope.comment);
        comment.user = $scope.user;
        comment.doc = $scope.doc;

        $http.post('/api/docs/' + comment.doc.id + '/comments', {
          'comment': comment
        })
          .success(function (data) {
            comment.label = 'comment';
            comment.user = data.user;
            comment.created = data.created_at;
            $scope.stream.push(comment);
            $scope.comment.text = '';
          })
          .error(function (data) {
            console.error("Error posting comment: %o", data);
          });
      };

      $scope.activityOrder = function (activity) {
        // Leaving this function in case we want to implement a more complex
        // ordering algorithm in the future
        return activity.likes;
      };

      $scope.addAction = function (activity, action, $event) {
        if ($scope.user && $scope.user.id !== '') {
          $http.post('/api/docs/' + $scope.doc.id + '/' + activity.label + 's/' + activity.id + '/' + action)
            .success(function (data) {
              activity.likes = data.likes;
              activity.flags = data.flags;
            }).error(function (data) {
              console.error(data);
            });
        } else {
          loginPopupService.showLoginForm($event);
        }

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

      $scope.subcommentSubmit = function (activity, subcomment) {
        subcomment.user = $scope.user;

        $.post('/api/docs/' + $scope.doc.id + '/' + activity.label + 's/' + activity.id + '/comments', {
          'comment': subcomment
        })
          .success(function (data) {
            activity.comments.push(data);
            subcomment.text = '';
            subcomment.user = '';
            $scope.$apply();
          }).error(function (data) {
            console.error(data);
          });
      };

      $scope.showAnnotations = function(annotationGroup) {
        $scope.annotationsShow = true;
        $scope.currentGroup = annotationGroup;
      };

      $scope.hideAnnotations = function() {
        $scope.annotationsShow = false;
        $scope.currentGroup = null;
      };
    }
    ]);
