angular.module('madisonApp.controllers')
  .controller('GroupManagementController', ['$scope', '$http', 'SessionService',
    function ($scope, $http, SessionService) {
      $scope.user = SessionService.getUser();

      $scope.$on('sessionChanged', function () {
        $scope.user = SessionService.getUser();
      });

      if ($scope.user) {
        $http.get('/api/user/' + $scope.user.id + '/groups')
          .success(function (data) {
            $scope.groups = data;

            angular.forEach($scope.groups, function (group) {
              group.canEdit = (group.role === 'owner' || group.role === 'editor');
            });
          });
      } else {
        console.log($scope.user, SessionService.getUser(), SessionService);
      }
      
    }]);