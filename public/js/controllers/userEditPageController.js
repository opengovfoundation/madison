angular.module('madisonApp.controllers')
  .controller('UserEditPageController', ['$scope', 'SessionService',
    'AuthService', 'growl', '$translate', 'pageService', 'SITE',
    function ($scope, SessionService, AuthService, growl, $translate,
      pageService, SITE) {
      $translate('content.edituser.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.verified = SessionService.verified;

      $scope.$on('sessionChanged', function () {
        $scope.verified = SessionService.verified;
      });

      $scope.saveUser = function () {
        //If the user is changing their password
        if (!!$scope.user.password) {
          //Check that the passwords match
          if (angular.equals($scope.user.password, $scope.password_confirmation)) {
            AuthService.saveUser($scope.user);
          } else {
            growl.error($translate.instant('errors.resetpassword.passwordmatch'));
          }
        } else { //If the user is not changing their password
          //Just go ahead and save
          AuthService.saveUser($scope.user);
        }

        $scope.password_confirmation = null;
      };
    }]);
