angular.module('madisonApp.directives')
  .directive('docListItem', function ($timeout) {
    return {
      restrict: 'A',
      templateUrl: '/templates/directives/doc-list-item.html',
      link: function(scope, el, attrs) {
        $timeout(function() {
          $('.discussion-closed').tooltip({});
        }, 1000);
      }
    };
  });
