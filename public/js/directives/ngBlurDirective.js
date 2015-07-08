angular.module('madisonApp.directives')
  .directive('ngBlur', function () {
    return function (scope, elem, attrs) {
      elem.bind('blur', function () {
        scope.$apply(attrs.ngBlur);
      });
    };
  });