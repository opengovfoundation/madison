angular.module('madisonApp.controllers')
  .controller('PasswordResetLandingController', ['$scope', '$stateParams', '$http', '$state', 'growl',
    function ($scope, $stateParams, $http, $state, growl) {

      $scope.token = $stateParams.token;

      $scope.savePassword = function () {
        if ($scope.password !== $scope.password_confirmation) {
          growl.error('The passwords do not match.');
          return;
        }

        $http.post('/api/password/reset', {
          email: $scope.email,
          password: $scope.password,
          password_confirmation: $scope.password_confirmation,
          token: $scope.token
        }).success(function () {
          $state.go('login');
        }).error(function (response) {
          console.error(response);
        });
      };
    }]);