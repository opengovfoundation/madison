angular.module('madisonApp.controllers')
  .controller('DashboardVerifyGroupController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];

      $scope.init = function () {
        $scope.getRequests();
      };

      $scope.getRequests = function () {
        $http.get('/api/groups/verify')
          .success(function (data, status, headers, config) {
            $scope.requests = data;
          })
          .error(function(data, status, headers, config){
            console.error(data);
          });
      };

      $scope.update = function(request, status, event){
          $http.post('/api/groups/verify', {'request': request, 'status': status})
          .success(function(data){
              request.status = status;
          })
          .error(function(data, status, headers, config){
              console.error(data);
          });
      };
    }]);