angular.module('madisonApp.directives')
  .directive('commentActions', ['loginPopupService', 'SessionService', '$http',
    function(loginPopupService, SessionService, $http) {
    return {
      restrict: 'E',
      templateUrl: '/templates/partials/comment-actions.html',
      scope: {
        obj: '=object'
      },
      controller: function ($scope) {
        $scope.user = SessionService.user;
        $scope.addAction = function (activity, action, $event) {
          console.log($scope.user, activity, action);
          if ($scope.user && $scope.user.id !== '') {
            $http.post('/api/docs/' + activity.doc_id + '/' + activity.label + 's/' + activity.id + '/' + action)
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
      }
    };
  }]);
