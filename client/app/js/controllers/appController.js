angular.module('madisonApp.controllers')
  .controller('AppController', ['$rootScope', '$scope', 'AuthService',
      'USER_ROLES', 'SessionService', 'loginPopupService', 'pageService',
      '$location', '$anchorScroll', 'prompts',
    function ($rootScope, $scope, AuthService, USER_ROLES, SessionService,
        loginPopupService, pageService, $location, $anchorScroll, prompts) {
      "use strict";

      $scope.user = null;
      $scope.userRoles = USER_ROLES;
      $scope.isAuthorized = AuthService.isAuthorized;
      $scope.loggingIn = loginPopupService.loggingIn;
      $scope.loggingInState = null;
      $scope.page = pageService;

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

      AuthService.getUser();

      if (!AuthService.isAuthenticated()) {
        prompts.info('Want to help craft legislation?  <a href="/user/signup">Create an account to annotate and comment &raquo;</a>');
      }

      //Set active group from the account dropdown
      $scope.setActiveGroup = function (groupId) {
        AuthService.setActiveGroup(groupId);
      };

      $scope.removeActiveGroup = function () {
        AuthService.removeActiveGroup();
      };

      $scope.scrollTo = function (id) {
        $location.hash(id);
        $anchorScroll();
      };
    }]);
