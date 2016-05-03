angular.module('madisonApp.directives')
.directive('showFocus', ['$timeout', function($timeout) {
  return function(scope, element, attrs) {
    scope.$watch(attrs.showFocus, function(newValue) {
      $timeout(function() {
        newValue && element.focus();
      }, true);
    });
  };
}]);
