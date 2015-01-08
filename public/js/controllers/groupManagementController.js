angular.module('madisonApp.controllers')
  .controller('GroupManagementController', ['$scope', 'UserService',
    function ($scope, UserService) {

      $scope.$on('groupsUpdated',
        function () {
          $scope.groups = UserService.groups;
        });

      UserService.getGroups();
    }]);