angular.module('madisonApp.controllers')
  .controller('CommentController', ['$scope', '$sce', '$http', 'annotationService', 'loginPopupService', 'growl', '$location', '$filter', '$timeout',
    function ($scope, $sce, $http, annotationService, loginPopupService, growl, $location, $filter, $timeout) {
      $scope.supported = null;
      $scope.opposed = false;
      $scope.collapsed_comment = {};
      $scope.showReplyForm = {};
      $scope.showReplies = {};
      $scope.loadingReplies = {};

      // Parse comment/subcomment direct links
      var hash = $location.hash();
      var subCommentId = hash.match(/(sub)?comment_([0-9]+)$/);
      if (subCommentId) {
        $scope.subCommentId = subCommentId[2];
      }

      $scope.doc.$promise.then(function () {
        $scope.getDocComments();
      });

      $scope.getDocComments = function () {
        var docId = $scope.doc.id;

        // Get all doc comments, regardless of nesting level
        $http({
          method: 'GET',
          url: '/api/docs/' + docId + '/comments'
        })
          .success(function (data) {

            // Build child-parent relationships for each comment
            angular.forEach(data, function (comment) {

              // If this isn't a parent comment, we need to find the parent and push this comment there
              if (comment.parent_id !== null) {
                var parent = $scope.parentSearch(data, comment.parent_id);
                comment.parentpointer = data[parent];
                data[parent].comments.push(comment);
              }

              // If this is the comment being linked to, save it
              if (comment.id === $scope.subCommentId) {
                $scope.collapsed_comment = comment;
              }

              comment.commentsCollapsed = true;
              comment.label = 'comment';
              comment.link = 'comment_' + comment.id;
              comment.comments = [];

              // We only want to push top-level comments, they will include
              // subcomments in their comments array(s)
              if (comment.parent_id === null) {
                $scope.doc.comments.push(comment);
              }
            });

            // If we are linking directly to a comment, we need to expand comments
            if ($scope.subCommentId) {
              var not_parent = true;
              // Expand comments, moving up towards the parent, until all are expanded
              do {
                $scope.collapsed_comment.commentsCollapsed = false;
                if ($scope.collapsed_comment.parent_id !== null) {
                  $scope.collapsed_comment = $scope.collapsed_comment.parentpointer;
                } else {
                  // We have reached the first sublevel of comments, so set the top level
                  // parent to expand and exit
                  not_parent = false;
                }
              } while (not_parent === true);
            }
          })
          .error(function (data) {
            console.error("Error loading comments: %o", data);
          });
      };

      $scope.isSponsor = function () {
        var currentId = $scope.user.id;
        var sponsored = false;

        angular.forEach($scope.doc.sponsor, function (sponsor) {
          if (currentId === sponsor.id) {
            sponsored = true;
          }
        });

        return sponsored;
      };

      // $scope.notifyAuthor = function (activity) {

      //   // If the current user is a sponsor and the activity hasn't been seen yet,
      //   // post to API route depending on comment/annotation label
      //   $http.post('/api/docs/' + doc.id + '/' + 'comments/' + activity.id + '/' + 'seen')
      //     .success(function (data) {
      //       activity.seen = data.seen;
      //     }).error(function (data) {
      //       console.error("Unable to mark activity as seen: %o", data);
      //     });
      // };

      $scope.parentSearch = function (arr, val) {
        var i;
        for (i = 0; i < arr.length; i++) {
          if (arr[i].id === val) {
            return i;
          }
        }

        return false;
      };

      $scope.commentSubmit = function (comment) {
        comment.user = $scope.user;
        comment.doc = $scope.doc;

        $http.post('/api/docs/' + comment.doc.id + '/comments', {
          'comment': comment
        })
          .success(function (data) {
            data.label = 'comment';
            $scope.doc.comments.push(data);
            comment.text = '';
          })
          .error(function (data) {
            console.error("Error posting comment: %o", data);
          });
      };

      $scope.activityOrder = function (activity) {
        var popularity = activity.likes - activity.dislikes;

        return popularity;
      };

      $scope.collapseComments = function (activity) {
        activity.commentsCollapsed = !activity.commentsCollapsed;
      };

      $scope.subcommentSubmit = function (activity, subcomment) {
        subcomment.user = $scope.user;

        $.post('/api/docs/' + $scope.doc.id + '/' + activity.label + 's/' + activity.id + '/comments', {
          'comment': subcomment
        })
          .success(function (data) {
            data.comments = [];
            data.label = 'comment';
            activity.comments.push(data);
            subcomment.text = '';
            subcomment.user = '';
          }).error(function (data) {
            console.error(data);
          });
      };

      $scope.toggleReplies = function(comment, $event) {
        if(comment.replyCount > 0 && comment.comments.length === 0) {
          $scope.loadingReplies[comment.id] = true;
          $http({
            method: 'GET',
            url: '/api/docs/' + comment.doc_id + '/comments',
            params: {'parent_id' : comment.id}
          })
          .success(function (data) {
            comment.comments = data;
            var commentsLength = comment.comments.length;
            for(i = 0; i < commentsLength; i++) {
              comment.comments[i].label = 'comment';
            }
            $scope.toggleReplies(comment, $event);
            $scope.loadingReplies[comment.id] = false;
          });
        }
        else {
          if($scope.showReplies[comment.id] === 'undefined' || !$scope.showReplies[comment.id]) {
            $scope.showReplies[comment.id] = true;
          }
          else {
            $scope.showReplies[comment.id] = false;
          }
        }
      };

      $scope.toggleReplyForm = function(comment, $event) {
        if ($scope.user && $scope.user.id !== '') {
          if($scope.showReplyForm[comment.id] === 'undefined' || !$scope.showReplyForm[comment.id]) {
            $scope.showReplyForm[comment.id] = true;
          }
          else {
            $scope.showReplyForm[comment.id] = false;
          }
        } else {
          loginPopupService.showLoginForm($event);
        }

      };

      $scope.showLoginForm = function($event) {
        loginPopupService.showLoginForm($event);
      };

    }
    ]);
