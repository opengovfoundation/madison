angular.module('madisonApp.controllers')
  .controller('GroupManagementController', ['$scope', '$http', 'UserService',
    function ($scope, $http, UserService) {

      $scope.$on('groupsUpdated', 
        function () {
          $scope.groups = UserService.groups;
        });

      UserService.getGroups();
    }]);