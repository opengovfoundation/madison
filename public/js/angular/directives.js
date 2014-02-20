app.directive('docComments', function(){
	return {
		restrict: 'AECM',
		templateUrl: '/templates/doc-comments.html'
	};
});

app.directive('ngBlur', function() {
  return function( scope, elem, attrs ) {
    elem.bind('blur', function() {
      scope.$apply(attrs.ngBlur);
    });
  };
});

app.directive('docLink', function($http, $compile){

	function link(scope, elem, attrs){

		$http.get('/api/docs/' + attrs.docId)
		.success(function(data){
			html = '<a href="/docs/' + data.slug + '">' + data.title + '</a>';
			e = $compile(html)(scope);
			elem.replaceWith(e);
		}).error(function(data){
			console.error("Unable to retrieve document %o: %o", attrs.docId, data);
		});
		
	}

	return {
		restrict: 'AECM',
		link: link
	};
});