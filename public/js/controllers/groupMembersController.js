angular.module('madisonApp.controllers')
  .controller('GroupMembersController', ['$scope', '$stateParams', 'Group',
    function ($scope, $stateParams, Group) {
      $scope.group = Group.get({id: $stateParams.id});

      $scope.group.$promise.then(function () {
        $scope.group.members = [];
        $scope.group.roles = [];

        $scope.group.roles = Group.getRoles();

        Group.getMembers({id: $stateParams.id}).$promise.then(
          function (response) {
            angular.forEach(response, function (member) {
              $scope.group.members.push(member);
            });
          }
        );
      });

      $scope.updateMemberRole = function (member) {
        Group.updateMemberRole({id: $stateParams.id, memberId: member.id, memberRole: member.role});
      };

      $scope.inviteMember = function () {
        console.log($scope.email, $scope.role);
        Group.inviteMember({id: $stateParams.id, email: $scope.email, role: $scope.role});
      };
    }]);