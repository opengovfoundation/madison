angular.module('madisonApp.controllers')
  .controller('GroupEditController', ['$scope', '$stateParams', 'Group',
    function ($scope, $stateParams, Group) {
      $scope.groupId = $stateParams.groupId;

      if ($scope.groupId) {
        $scope.group = Group.get({id: $scope.groupId});
      } else {
        $scope.group = new Group();
      }

      $scope.saveGroup = function () {
        console.log($scope.group);
        $scope.group.$save();
      };

    }]);