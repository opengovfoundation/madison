$(document).ready(function(){
	var annotator = $('#content').annotator();

	annotator.annotator('addPlugin', 'Unsupported');
	annotator.annotator('addPlugin', 'Tags');
	annotator.annotator('addPlugin', 'Markdown');
	annotator.annotator('addPlugin', 'Store', {
		annotationData:{
			'uri': window.location.href
		},
		prefix: '/api/annotations',
		loadFromSearch: {
			'uri': window.location.href
		},
		urls: {
			create: 	'',
			read: 		'/:id',
			update: 	'/:id',
			destroy: 	'/:id',
			search: 	'/search'
		}
	});
});