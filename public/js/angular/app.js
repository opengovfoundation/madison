imports = ['angular-md5', 'ui.bootstrap', 'ui.utils', 'ui.select2'];

var app = angular.module('madisonApp', imports, function($interpolateProvider){
	$interpolateProvider.startSymbol('<%');
	$interpolateProvider.endSymbol('%>');
});

function getAnnotationService(){

	var elem = angular.element($('html'));
	var injector = elem.injector();
	annotationService = injector.get('annotationService');

	return annotationService;
}