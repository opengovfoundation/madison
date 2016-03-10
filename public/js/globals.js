/*global window*/

var ANNOTATION_HASH_REGEX = /^annotation_([0-9]+)$/;
var ANNOTATION_COMMENT_HASH_REGEX = /^annsubcomment_([0-9]+)$/;
var COMMENT_HASH_REGEX = /^comment_([0-9]+)-?([0-9]+)?$/;

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

window.getAuthService = function () {
  var elem = angular.element($('html'));
  var injector = elem.injector();
  var AuthService = injector.get('AuthService');

  return AuthService;
};

/**
 * Needed for IE9 support
 */
window.getBasePath = function() {
  var basePath = window.location.href;
  var firstHashIndex = basePath.indexOf('#');
  if (firstHashIndex !== -1) {
    var secondHashIndex = basePath.indexOf('#', firstHashIndex + 1);
    if (secondHashIndex !== -1) {
      basePath = basePath.substring(0, secondHashIndex);
    } else {
      basePath = basePath.substring(0, firstHashIndex);
    }
  }
  return basePath;
};
