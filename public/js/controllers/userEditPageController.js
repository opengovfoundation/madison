angular.module('madisonApp.controllers')
  .controller('UserEditPageController', ['$scope', 'SessionService', 'AuthService', 'growl',
    function ($scope, SessionService, AuthService, growl) {
      $scope.verified = SessionService.verified;

      $scope.$on('sessionChanged', function () {
        $scope.verified = SessionService.verified;
      });

      $scope.isUserVerified = function () {
        if ($scope.user.user_meta) {
          angular.forEach($scope.user.user_meta, function (meta) {
            if (meta.meta_key === 'verify') {
              $scope.user.verified = meta.meta_value;
            }
          });
        } else {
          $scope.user.verified = false;
        }
      };

      $scope.saveUser = function () {
        //If the user is changing their password
        if (!!$scope.user.password) {
          //Check that the passwords match
          if (angular.equals($scope.user.password, $scope.password_confirmation)) {
            AuthService.saveUser($scope.user);
          } else {
            growl.error("The password fields do not match.");
          }
        } else { //If the user is not changing their password
          //Just go ahead and save
          AuthService.saveUser($scope.user);
        }

        $scope.password_confirmation = null;
      };
    }]);
