function getAnnotationService(){
    var elem = angular.element($('html'));
    var injector = elem.injector();
    annotationService = injector.get('annotationService');

    return annotationService;
}