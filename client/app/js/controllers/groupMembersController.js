angular.module('madisonApp.controllers')
  .controller('GroupMembersController', ['$scope', '$stateParams', 'Group',
    '$state', '$translate', 'pageService', 'SITE',
    function ($scope, $stateParams, Group, $state, $translate, pageService,
    SITE) {
      $translate('content.groupmembers.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

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
        Group.updateMemberRole({id: $stateParams.id, memberId: member.id,
          memberRole: member.role});
      };

      $scope.inviteMember = function () {
        Group.inviteMember({id: $stateParams.id, email: $scope.email,
          role: $scope.role}).$promise.then(function () {
            $state.go('manage-group-members', {id: $stateParams.id});
          }
        );
      };

      $scope.removeMember = function (index) {
        var member = $scope.group.members[index];

        Group.removeMember({id: $stateParams.id, memberId: member.id}).$promise
          .then(function (response) {
            //Remove member from members array if response a success
            if (response.messages[0].severity === 'success') {
              $scope.group.members.splice(index, 1);
            }
          }
        );
      };
    }]);
