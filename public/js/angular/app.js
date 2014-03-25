imports = ['madison.dateFilters', 'angular-md5', 'ui.bootstrap', 'ui.utils', 'ui.select2', 'ui.bootstrap.datetimepicker', 'ngAnimate'];

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