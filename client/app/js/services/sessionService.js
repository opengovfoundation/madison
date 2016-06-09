angular.module('madisonApp.services')
  .service('SessionService', function ($rootScope, $filter) {
    "use strict";

    this.user = null;
    this.groups = [];
    this.activeGroup = null;
    this.groupedDocs = {};

    this.create = function (user, groups, activeGroupId) {
      this.user = user;

      if (typeof groups !== 'undefined') {
        this.groups = groups;

        if (activeGroupId !== null) {
          this._setActiveGroup(activeGroupId);
        } else {
          //If there is currently an active group, remove it
          if(this.activeGroup !== null) {
            this.activeGroup = null;

            $rootScope.$broadcast('activeGroupChanged');
          }

        }
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

    this.getActiveGroup = function () {
      return this.activeGroup;
    };

    this.setDocs = function (groupedDocs) {
      this.groupedDocs = groupedDocs;

      $rootScope.$broadcast('docsChanged');
    };

    this.getDocs = function () {
      return this.groupedDocs;
    };

    this._setActiveGroup = function (groupId) {
      var activeGroup = $filter('getById')(this.groups, groupId);

      this.activeGroup = activeGroup;

      $rootScope.$broadcast('activeGroupChanged');
    };

    this.setPreviousState = function (previous) {
      this.previousState = previous;
    };

    this.getPreviousState = function () {
      return this.previousState;
    };

    return this;
  });
