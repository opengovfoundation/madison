angular.module('madisonApp.controllers')
  .controller('PasswordResetLandingController', ['$scope', '$stateParams',
    '$http', '$state', 'growl', '$translate', 'pageService', 'SITE',
    function ($scope, $stateParams, $http, $state, growl, $translate,
      pageService, SITE) {

      pageService.setTitle($translate.instant('content.resetpassword.title',
        {title: SITE.name}));

      $scope.token = $stateParams.token;

      $scope.savePassword = function () {
        if ($scope.password !== $scope.password_confirmation) {
          growl.error($translate.instant('errors.resetpassword.passwordmatch'));
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
          growl.error($translate.instant('errors.resetpassword.general'));
          console.error(response);
        });
      };
    }]);
