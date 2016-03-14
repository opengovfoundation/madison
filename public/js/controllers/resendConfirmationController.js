angular.module('madisonApp.controllers')
  .controller('ResendConfirmationController', ['$scope', '$http', '$state',
    '$stateParams', '$translate', 'pageService', 'SITE', '$timeout',
    function ($scope, $http, $state, $stateParams, $translate, pageService,
      SITE, $timeout) {
      pageService.setTitle($translate.instant('content.confirmationresend.title',
        {title: SITE.name}));

      // We do this in a `$timeout` because in IE9, when going
      // from regular URL to non-HTML5 version, the controller loads twice and
      // causes the token to be submitted twice.
      $timeout(function() {
        //If we're following a link from the verification email
        if ($stateParams.token) {
          console.log('posting token');
          $http.post('/api/user/verify-email', {
            token: $stateParams.token
          }).success(function (response) {
            $state.go('index');
          }).error(function (response) {
            $state.go('login');
            console.error(response);
          });
        }
      }, 500);

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
