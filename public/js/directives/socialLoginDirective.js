angular.module('madisonApp.directives')
  .directive('socialLogin', [ function () {
    return {
      restrict: 'A',
      scope: {
        message: '@message'
      },
      templateUrl: '/templates/social-login.html'
    };
  }]);