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
