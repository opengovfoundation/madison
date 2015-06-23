angular.module('madisonApp.directives')
  .directive('docListItem', function () {
    return {
      restrict: 'A',
      templateUrl: '/templates/directives/doc-list-item.html'
    };
  });
