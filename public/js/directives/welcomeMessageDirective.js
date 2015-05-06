angular.module('madisonApp.directives')
  .directive('welcomeMessage', function () {
    return {
      restrict: 'A',
      templateUrl: '/templates/partials/welcome.html'
    };
  });
