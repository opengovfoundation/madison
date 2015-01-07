angular.module('madisonApp.controllers')
  .controller('DashboardVerifyController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];

      $scope.init = function () {
        $scope.getRequests();
      };

      $scope.getRequests = function () {
        $http.get('/api/user/verify')
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/user/verify', {
          'request': request,
          'status': status
        })
          .success(function () {
            request.meta_value = status;
          })
          .error(function (data) {
            console.error(data);
          });
      };
    }
    ]);