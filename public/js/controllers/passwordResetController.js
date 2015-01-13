angular.module('madisonApp.controllers')
  .controller('PasswordResetController', ['$scope', '$http', '$state',
    function ($scope, $http, $state) {

      $scope.reset = function () {
        $http.post('/api/password/remind', {email: $scope.email})
          .success(function () {
            $state.go('login');
          }).error(function (response) {
            console.error(response);
          });
      };

    }]);