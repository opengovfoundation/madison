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
      loginPopupService._setLocation = function (left, top) {
        this.top = top;
        this.left = left;
      };

      //Toggle the view state
      loginPopupService.showLoginForm = function (event) {
        this._setLocation(event.pageX, event.pageY);
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
