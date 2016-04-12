angular.module('madisonApp.controllers')
  .controller('GroupEditController', ['$scope', '$state', '$stateParams',
    'Group', '$translate', 'pageService', 'SITE',
    function ($scope, $state, $stateParams, Group, $translate, pageService,
      SITE) {

      $scope.groupId = $stateParams.groupId;

      var title = '';
      if ($scope.groupId) {
        $scope.group = Group.get({id: $scope.groupId});
        title = 'content.editgroup.title';
      } else {
        $scope.group = new Group();
        title = 'content.creategroup.title';
      }

      $translate(title, {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.saveGroup = function () {
        var request = $scope.group.$save(function (group, headers) {
          $state.go('group-management');
        });
      };

    }]);
