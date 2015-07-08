angular.module('madisonApp.controllers')
  .controller('DashboardNotificationsController', ['$scope', '$http',
    function ($scope, $http) {

      $http.get('/api/user/' + $scope.user.id + '/notifications')
        .success(function (data) {
          $scope.notifications = data;
        }).error(function (data) {
          console.error("Error loading notifications: %o", data);
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