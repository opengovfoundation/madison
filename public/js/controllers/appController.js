angular.module('madisonApp.controllers')
  .controller('AppController', ['$rootScope', '$scope', 'AuthService', 'USER_ROLES', 'AUTH_EVENTS', 'SessionService', 'growl', '$state', 'loginPopupService',
    function ($rootScope, $scope, AuthService, USER_ROLES, AUTH_EVENTS, SessionService, growl, $state, loginPopupService) {
      $scope.user = null;
      $scope.userRoles = USER_ROLES;
      $scope.isAuthorized = AuthService.isAuthorized;
      $scope.loggingIn = loginPopupService.loggingIn;
      $scope.loggingInState = null;

      $scope.$on('loggingIn', function () {
        $scope.loggingIn = loginPopupService.loggingIn;
        $scope.loggingInState = loginPopupService.state;
      });

      $scope.$on('sessionChanged', function () {
        $scope.user = SessionService.getUser();
        $scope.groups = SessionService.getGroups();
      });

      $scope.$on(AUTH_EVENTS.notAuthenticated, function () {
        growl.error("You must log in to view this page.");
        $state.go('index');
      });

      /*jslint unparam: true*/
      $rootScope.$on('$stateChangeSuccess', function (ev, to, toParams, from, fromParams) {
        $scope.prevState = {
          from: from,
          fromParams: fromParams
        };
      });
      /*jslint unparam: false*/

      AuthService.getUser();
    }]);
