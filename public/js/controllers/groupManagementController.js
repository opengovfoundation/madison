angular.module('madisonApp.controllers')
  .controller('GroupManagementController', ['$scope', 'UserService',
    function ($scope, UserService) {

      $scope.$on('groupsUpdated',
        function () {
          $scope.groups = UserService.groups;

          angular.forEach($scope.groups, function (group) {
            group.canEdit = (group.role === 'owner' || group.role === 'editor');
          });
        });

      UserService.getGroups();
    }]);