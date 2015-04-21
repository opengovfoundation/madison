angular.module('madisonApp.controllers')
  .controller('AppController', ['$rootScope', '$scope', 'AuthService', 'USER_ROLES', 'SessionService', 'loginPopupService', '$location', '$anchorScroll',
    function ($rootScope, $scope, AuthService, USER_ROLES, SessionService, loginPopupService, $location, $anchorScroll) {
      "use strict";

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

      $scope.$on('activeGroupChanged', function () {
        $scope.activeGroup = SessionService.getActiveGroup();
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

      //Set active group from the account dropdown
      $scope.setActiveGroup = function (groupId) {
        AuthService.setActiveGroup(groupId);
      };

      $scope.removeActiveGroup = function () {
        AuthService.removeActiveGroup();
      };

      $scope.scrollTo = function (id) {
        console.log('Scrolling to ' + id);
        $location.hash(id);
        $anchorScroll();
      };
    }]);
