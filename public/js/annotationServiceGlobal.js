//var angular = require('angular');

window.getAnnotationService = function () {
  var elem = angular.element($('html'));
  var injector = elem.injector();
  var annotationService = injector.get('annotationService');

  return annotationService;
};
