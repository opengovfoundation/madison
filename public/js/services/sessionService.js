angular.module('madisonApp.services')
  .service('SessionService', function ($rootScope) {
    this.user = null;
    this.groups = [];

    this.create = function (user, groups) {
      this.user = user;

      if (typeof groups !== 'undefined') {
        this.groups = groups;
      }

      $rootScope.$broadcast('sessionChanged');
    };

    this.destroy = function () {
      this.user = null;

      $rootScope.$broadcast('sessionChanged');
    };

    this.getUser = function () {
      return this.user;
    };

    this.getGroups = function () {
      return this.groups;
    };

    return this;
  });
