angular.module('madisonApp.controllers')
  .controller('CommentController',
    ['$scope', '$sce', '$http', 'annotationService', 'loginPopupService',
      'growl', '$location', '$filter', '$timeout', '$anchorScroll',
    function ($scope, $sce, $http, annotationService, loginPopupService,
      growl, $location, $filter, $timeout, $anchorScroll) {

      $scope.supported = null;
      $scope.opposed = false;
      $scope.showReplyForm = {};
      $scope.showReplies = {};
      $scope.loadingReplies = {};
      $scope.commentId = null;
      $scope.subCommentId = null;

      // Parse comment/subcomment direct links
      var hash = $location.hash();
      var commentId = hash.match(COMMENT_HASH_REGEX);

      if (commentId) {
        // If it's a subcomment link, it will be list of parent-child chain
        $scope.commentId = parseInt(commentId[1]);
        if (commentId[2]) $scope.subCommentId = parseInt(commentId[2]);
      }

      $scope.doc.$promise.then(function () {
        $scope.getDocComments();
      });

      // TODO: this method exists here and in `annotationController`
      $scope.getDocComments = function () {
        var docId = $scope.doc.id;
        // Get all doc comments, regardless of nesting level
        $http({
          method: 'GET',
          url: '/api/docs/' + docId + '/comments?is_ranged=false&include_replies=false'
        })
        .success(function (data) {
          // Convert links in the comments to html
          $.each(data, function () {
              this.text = Autolinker.link(this.text, {newWindow: true});
          });

          // API only returns top level comments initially, the rest are
          // grabbed as needed from the "show replies" link
          angular.forEach(data, function (comment) {
            comment.commentsCollapsed = true;
            comment.permalinkBase = 'comment';
            comment.label = 'comment';
            comment.link = 'comment_' + comment.id;
            comment.comments = [];

            $scope.doc.comments.push(comment);

            if ($scope.subCommentId && comment.id === $scope.commentId) {
              $scope.toggleReplies(comment);
            }
          });

          var offInitialSubCommentsLoaded = $scope.$on('commentRepliesShown', function() {
            $timeout($anchorScroll, 0); // Next tick will have subcomments rendered
            offInitialSubCommentsLoaded(); // This deregisters the event, so it only happens once
          });

          // Next tick will have comments rendered
          $timeout($anchorScroll, 0);
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

        $http.post('/api/docs/' + comment.doc.id + '/comments', comment)
          .success(function (data) {
            data.permalinkBase = 'comment';
            data.label = 'comment';
            $scope.doc.comments.push(data);
            comment.text = '';
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

      $scope.collapseComments = function (activity) {
        activity.commentsCollapsed = !activity.commentsCollapsed;
      };

      // TODO: duplicate in `annotationController`, move somewhere shared
      $scope.subcommentSubmit = function (activity, subcomment) {
        subcomment.user = $scope.user;

          $.post('/api/docs/' + $scope.doc.id + '/comments/' + activity.id + '/comments', subcomment)
          .success(function (data) {
            data.comments = [];
            data.permalinkBase = 'comment';
            data.label = 'comment';
            if(!activity.comments) {
              activity.comments = [];
            }
            activity.comments.push(data);
            // Show our comment.
            if(activity.label === 'comment') {
              $scope.showReplies[activity.id] = true;
            }
            // Clear the submit box.
            subcomment.text = '';
            subcomment.user = '';
          }).error(function (data) {
            console.error(data);
          });
      };

      $scope.toggleReplies = function(comment, $event) {
        if (comment.comments_count > 0 && comment.comments.length === 0) {
          $scope.loadingReplies[comment.id] = true;
          return $http({
            method: 'GET',
            url: '/api/docs/' + $scope.doc.id + '/comments',
            params: {'parent_id' : comment.id}
          })
          .success(function (data) {
            // Convert links in the replies to html
            $.each(data, function () {
              this.text = Autolinker.link(this.text, {newWindow: true});
            });

            comment.comments = data;
            var commentsLength = comment.comments.length;
            for (i = 0; i < commentsLength; i++) {
              comment.comments[i].permalinkBase = 'comment';
              comment.comments[i].label = 'comment';
              comment.comments[i].parentPointer = comment;
            }
            $scope.toggleReplies(comment, $event);
            $scope.loadingReplies[comment.id] = false;
          });
        } else {
          if ($scope.showReplies[comment.id] === 'undefined' || !$scope.showReplies[comment.id]) {
            $scope.showReplies[comment.id] = true;
            $scope.$broadcast('commentRepliesShown');
          } else {
            $scope.showReplies[comment.id] = false;
            $scope.$broadcast('commentRepliesHidden');
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

      $scope.shouldHighlightComment = function(comment) {
        // Only highlight top level comment if we're
        // *not* higlighting a subcomment
        if (!$scope.subCommentId && comment.id === $scope.commentId) {
          return true;
        } else {
          return false;
        }
      };

      $scope.shouldHighlightSubComment = function(comment) {
        if (comment.id === $scope.subCommentId) {
          return true;
        } else {
          return false;
        }
      };

      $scope.showLoginForm = function($event) {
        loginPopupService.showLoginForm($event);
      };

    }
    ]);
