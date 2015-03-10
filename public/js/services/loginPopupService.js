angular.module('madisonApp.services')
  .factory('loginPopupService', ['$rootScope',
    function ($rootScope) {
      var loginPopupService = {
        loggingIn: false,
        state: null,
        top: 0,
        left: 0
      };

      //Set the screen location
      loginPopupService.setLocation = function (top, left) {
        this.top = top;
        this.left = left;
      };

      //Toggle the view state
      loginPopupService.showLoginForm = function (event) {
        this.setLocation(event.screenX, event.screenY);
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