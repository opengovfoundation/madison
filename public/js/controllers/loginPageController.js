angular.module('madisonApp.controllers')
  .controller('LoginPageController', ['$rootScope', '$scope', '$state', 'AuthService', 'SessionService',
    'growl', '$translate', 'pageService', '$location', '$http', 'SITE',
    function ($rootScope, $scope, $state, AuthService, SessionService, growl, $translate, pageService,
      $location, $http, SITE) {

      pageService.setTitle($translate.instant('content.login.title',
        {title: SITE.name}));

      $scope.credentials = {email: "", password: "", remember: false};

      /**
      * Attempt to log the user in
      *   Attempt to login with the form credentials,
      *     On success reset the credentials, reset the current user via
      *     getUser(), return to homepage with success message
      *     On error log the error to console.  If there are no growl error
      *     messages sent from the server, an error message is presented.
      */
      $scope.login = function () {
        var login = AuthService.login($scope.credentials);

        login.then(function () {
          var previousState = SessionService.getPreviousState();
          $scope.credentials = {email: "", password: "", remember: false};

          $state.go(previousState.name, previousState.fromParams);
        });
      };

      $scope.facebookLogin = function () {
        var login = AuthService.facebookLogin();
      };
    }]);
