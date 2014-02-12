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