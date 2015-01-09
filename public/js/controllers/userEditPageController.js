angular.module('madisonApp.controllers')
  .controller('UserEditPageController', ['$scope', 'UserService',
    function ($scope, UserService) {
      $scope.user = UserService.user;

      $scope.$on('userUpdated', function () {
        $scope.user = UserService.user;
        $scope.isUserVerified();
      });

      $scope.isUserVerified = function () {
        if ($scope.user.user_meta) {
          angular.forEach($scope.user.user_meta, function (meta) {
            if (meta.meta_key === 'verify') {
              $scope.user.verified = meta.meta_value;
            }
          });
        } else {
          $scope.user.verified = false;
        }
      };
    }]);