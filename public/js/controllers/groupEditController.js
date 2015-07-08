angular.module('madisonApp.controllers')
  .controller('GroupEditController', ['$scope', '$state', '$stateParams', 'Group',
    function ($scope, $state, $stateParams, Group) {
      $scope.groupId = $stateParams.groupId;

      if ($scope.groupId) {
        $scope.group = Group.get({id: $scope.groupId});
      } else {
        $scope.group = new Group();
      }

      $scope.saveGroup = function () {
        var request = $scope.group.$save(function (group, headers) {
          $state.go('group-management');
        });
      };

    }]);
