angular.module('madisonApp.services')
  .factory('loginPopupService', ['$rootScope',
    function ($rootScope) {
      var loginPopupService = {
        loggingIn: false,
        state: null
      };

      //Toggle the view state
      loginPopupService.showLoginForm = function (event) {
        this.loggingIn = true;
        $rootScope.$broadcast('loggingIn');
      };

      loginPopupService.closeLoginForm = function () {
        this.loggingIn = false;
        $rootScope.$broadcast('loggingIn');
      };

      return loginPopupService;
    }
    ]);
