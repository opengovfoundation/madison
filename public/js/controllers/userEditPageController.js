angular.module('madisonApp.controllers')
  .controller('UserEditPageController', ['$scope', 'SessionService',
    function ($scope, SessionService) {
      $scope.user = SessionService.user;
      $scope.verified = SessionService.verified;

      $scope.$on('sessionChanged', function () {
        $scope.user = SessionService.user;
        $scope.verified = SessionService.verified;
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