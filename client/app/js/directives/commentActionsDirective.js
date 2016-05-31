angular.module('madisonApp.directives')
  .directive('commentActions',
    ['loginPopupService', 'SessionService', '$http', '$location', 'growl',
    function(loginPopupService, SessionService, $http, $location, growl) {

    return {
      restrict: 'E',
      templateUrl: '/templates/partials/comment-actions.html',
      scope: {
        obj: '=object',
        rootTarget: '='
      },
      controller: function ($scope) {
        $scope.permalink = buildPermalink($scope.obj);

        $scope.user = SessionService.user;
        $scope.addAction = function (activity, action, $event) {
          if ($scope.user && $scope.user.id !== '') {
            $http.post('/api/docs/' + $scope.rootTarget.id + '/comments/' + activity.id + '/' + action)
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

        function buildPermalink(comment) {
          var hash = '#' + comment.permalinkBase + '_';

          if (comment.parent_id) {
            hash += comment.parent_id + '-' + comment.id;
          } else {
            hash += comment.id;
          }

          return window.getBasePath() + hash;
        }
      },
      compile: function() {
        return {
          post: function(scope, element, attrs) {
            var link = element.find('.link');
            var client = new ZeroClipboard(link);
            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.success("Link copied to clipboard.");
              });
            });
          }
        };
      }
    };
  }]);
