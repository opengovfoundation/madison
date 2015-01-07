angular.module('madisonApp.controllers')
  .controller('DashboardSettingsController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.admins = [];

      $scope.getAdmins = function () {
        $http.get('/api/user/admin')
          .success(function (data) {
            $scope.admins = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.saveAdmin = function (admin) {
        admin.saved = false;

        $http.post('/api/user/admin', {
          'admin': admin
        })
          .success(function () {
            admin.saved = true;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.init = function () {
        $scope.getAdmins();
      };
    }
    ]);