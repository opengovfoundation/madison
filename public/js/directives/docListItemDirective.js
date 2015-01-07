angular.module('madisonApp.directives')
  .directive('docListItem', function () {
    return {
      restrict: 'A',
      templateUrl: '/templates/doc-list-item.html'
    };
  });