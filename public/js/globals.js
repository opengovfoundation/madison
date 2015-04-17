/*global window*/

window.getAnnotationService = function () {
  var elem = angular.element($('html'));
  var injector = elem.injector();
  var annotationService = injector.get('annotationService');

  return annotationService;
};

window.getLoginPopupService = function () {
  var elem = angular.element($('html'));
  var injector = elem.injector();
  var loginPopupService = injector.get('loginPopupService');

  return loginPopupService;
};
