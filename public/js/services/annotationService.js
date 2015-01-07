angular.module('madisonApp.services')
  .factory('annotationService', function ($rootScope, $sce) {
    var annotationService = {};
    var converter = new Markdown.Converter();
    annotationService.annotations = [];

    annotationService.setAnnotations = function (annotations) {

      angular.forEach(annotations, function (annotation) {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);
      }, this);

      this.broadcastUpdate();
    };

    annotationService.addAnnotation = function (annotation) {
      if (annotation.id === undefined) {
        var interval = window.setInterval(function () {
          this.addAnnotation(annotation);
          window.clearInterval(interval);
        }.bind(this), 500);
      } else {
        annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
        this.annotations.push(annotation);
        this.broadcastUpdate();
      }
    };

    annotationService.broadcastUpdate = function () {
      $rootScope.$broadcast('annotationsUpdated');
    };

    return annotationService;
  });