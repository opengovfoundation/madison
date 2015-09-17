angular.module('madisonApp.controllers')
  .controller('LoginPageController', ['$rootScope', '$scope', '$state', 'AuthService', 'growl',
    function ($rootScope, $scope, $state, AuthService, growl) {
      $scope.credentials = {email: "", password: "", remember: false};

      /**
      * Attempt to log the user in
      *   Attempt to login with the form credentials, 
      *     On success reset the credentials, reset the current user via getUser(), return to homepage with success message
      *     On error log the error to console.  If there are no growl error messages sent from the server, an error message is presented.
      */
      $scope.login = function () {
        var login = AuthService.login($scope.credentials);

        login.then(function () {
          $scope.credentials = {email: "", password: "", remember: false};

          var url = 'index';
          if($rootScope.returnTo)
          {
            url = $rootScope.returnTo;
          }
          $state.go(url);

          growl.success("You have been logged in successfully");
        });
      };

      $scope.facebookLogin = function () {
        var login = AuthService.facebookLogin();
      };
    }]);