angular.module('madisonApp.controllers')
  .controller('UserEditPageController', ['$scope', 'UserService',
    function ($scope, UserService) {
      $scope.user = UserService.user;

      $scope.$on('userUpdated', function () {
        $scope.user = UserService.user;
      });
    }]);