angular.module('madisonApp.controllers')
  .controller('UserNotificationsController', ['$scope', '$http', 'UserService', function ($scope, $http, UserService) {
    //Wait for AppController controller to load user
    UserService.exists.then(function () {
      $http.get('/api/user/' + $scope.user.id + '/notifications')
        .success(function (data) {
          $scope.notifications = data;
        }).error(function (data) {
          console.error("Error loading notifications: %o", data);
        });
    });

    //Watch for notification changes
    $scope.$watch('notifications', function (newValue, oldValue) {
      if (oldValue !== undefined) {
        //Save notifications
        $http.put('/api/user/' + $scope.user.id + '/notifications', {notifications: newValue})
          .error(function (data) {
            console.error("Error updating notification settings: %o", data);
          });
      }
    }, true);

  }]);