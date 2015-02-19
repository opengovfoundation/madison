angular.module('madisonApp.services')
  .service('SessionService', function ($rootScope) {
    this.user = null;

    this.create = function (user) {
      this.user = user;

      $rootScope.$broadcast('sessionChanged');
    };

    this.destroy = function () {
      this.user = null;

      $rootScope.$broadcast('sessionChanged');
    };

    this.getUser = function () {
      return this.user;
    };

    return this;
  });