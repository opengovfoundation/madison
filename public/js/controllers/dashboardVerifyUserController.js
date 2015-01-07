angular.module('madisonApp.controllers')
  .controller('DashboardVerifyUserController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];

      $scope.init = function () {
        $scope.getRequests();
      };

      $scope.getRequests = function () {
        $http.get('/api/user/independent/verify')
          .success(function (data, status, headers, config) {
            $scope.requests = data;
          })
          .error(function (data, status, headers, config) {
            console.error(data);
          });
      };

      $scope.update = function (request, status, event) {
        $http.post('/api/user/independent/verify', {'request': request, 'status': status})
          .success(function (data) {
            request.meta_value = status;
            location.reload();
          })
          .error(function (data, status, headers, config) {
            console.error(data);
          });
      };
    }]);