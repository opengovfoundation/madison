angular.module('madisonApp.directives')
  .directive('socialLogin', [ 'AuthService',
    function (AuthService) {
      return {
        restrict: 'A',
        scope: {
          message: '@message'
        },
        templateUrl: '/templates/social-login.html',
        controller: function ($scope) {
          $scope.facebookLogin = function () {
            AuthService.facebookLogin();
          };
        }
      };
    }]);