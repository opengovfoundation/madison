angular.module('madisonApp.controllers')
  .controller('AppController', ['$rootScope', '$scope', 'AuthService', 'USER_ROLES', 'AUTH_EVENTS', 'SessionService', 'growl', '$state',
    function ($rootScope, $scope, AuthService, USER_ROLES, AUTH_EVENTS, SessionService, growl, $state) {
      $scope.currentUser = null;
      $scope.userRoles = USER_ROLES;
      $scope.isAuthorized = AuthService.isAuthorized;

      $scope.$on('sessionChanged', function () {
        $scope.setCurrentUser(SessionService.user);
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

      $scope.setCurrentUser = function (user) {
        $scope.currentUser = user;
      };
    }]);