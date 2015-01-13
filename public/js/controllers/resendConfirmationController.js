angular.module('madisonApp.controllers')
  .controller('ResendConfirmationController', ['$scope', '$http', '$state', '$stateParams',
    function ($scope, $http, $state, $stateParams) {

      //If we're following a link from the verification email
      if ($stateParams.token) {
        $http.post('/api/user/verify-email', {
          token: $stateParams.token
        }).success(function (response) {
          $state.go('index');
        }).error(function (response) {
          $state.go('login');
          console.error(response);
        });
      }

      $scope.resendConfirmation = function () {
        $http.post('/api/verification/resend', {
          email: $scope.email,
          password: $scope.password
        }).success(function (response) {
          $state.go('login');
        }).error(function (response) {
          console.error(response);
        });
      };
    }]);