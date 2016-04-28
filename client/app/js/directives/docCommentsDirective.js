angular.module('madisonApp.directives')
  .directive('docComments', function () {
    return {
      restrict: 'AECM',
      templateUrl: '/templates/doc-comments.html'
    };
  });