angular.module('madisonApp.controllers')
  .controller('DashboardVerifyUserController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];
      $scope.formdata = {
        'status' : 'pending'
      };

      $scope.getRequests = function () {
        $http.get('/api/user/independent/verify', { params: $scope.formdata } )
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/user/independent/verify', {'request': request, 'status': status})
          .success(function (data) {
            request.meta_value = status;
            location.reload();
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.getRequests();
    }]);
