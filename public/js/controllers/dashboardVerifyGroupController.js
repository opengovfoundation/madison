angular.module('madisonApp.controllers')
  .controller('DashboardVerifyGroupController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];

      $scope.getRequests = function () {
        $http.get('/api/groups/verify')
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/groups/verify', {'request': request, 'status': status})
          .success(function (data) {
            request.status = status;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.getRequests();
    }]);