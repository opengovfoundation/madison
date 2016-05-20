angular.module('madisonApp.controllers')
  .controller('GroupManagementController', ['$scope', '$http', 'SessionService',
  '$translate', 'pageService', 'SITE',
    function ($scope, $http, SessionService, $translate, pageService, SITE) {
      $translate('content.groupmanagement.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.user = SessionService.getUser();

      $scope.$on('sessionChanged', function () {
        $scope.user = SessionService.getUser();
      });

      if ($scope.user) {
        $http.get('/api/user/' + $scope.user.id + '/groups', { individual: false })
          .success(function (data) {
            $scope.groups = data;

            angular.forEach($scope.groups, function (group) {
              group.canEdit = (group.role === 'owner' || group.role === 'editor');
            });
          });
      } else {
        // TODO: Add error handling here.
        console.log($scope.user, SessionService.getUser(), SessionService);
      }

    }]);
