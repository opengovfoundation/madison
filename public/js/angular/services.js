app.factory('annotationService', function($rootScope, $sce){
	var annotationService = {};
	var converter = new Markdown.Converter();
	annotationService.annotations = [];

	annotationService.setAnnotations = function(annotations){

		angular.forEach(annotations, function(annotation, key){
			annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
			this.annotations.push(annotation);
		}, this);		

		this.broadcastUpdate();
	}

	annotationService.addAnnotation = function(annotation){
		if(typeof annotation.id == 'undefined'){
			interval = window.setInterval(function(){
				this.annotationService.addAnnotation(annotation);
				window.clearInterval(interval);
			}, 500);
		}else{
			annotation.html = $sce.trustAsHtml(converter.makeHtml(annotation.text));
			this.annotations.push(annotation);
			this.broadcastUpdate();
		}
	}

	annotationService.broadcastUpdate = function(){
		$rootScope.$broadcast('annotationsUpdated');
	}

	return annotationService;
});