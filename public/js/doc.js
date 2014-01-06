$(document).ready(function(){
	var current_user = $('#current_user').val();
	
	var annotator = $('#content').annotator({
		readOnly: current_user === undefined
	});

	annotator.annotator('addPlugin', 'Unsupported');
	annotator.annotator('addPlugin', 'Tags');
	annotator.annotator('addPlugin', 'Markdown');
	annotator.annotator('addPlugin', 'Store', {
		annotationData:{
			'uri': window.location.href
		},
		prefix: '/api/annotations',
		urls: {
			create: 	'',
			read: 		'/:id',
			update: 	'/:id',
			destroy: 	'/:id',
			search: 	'/search'
		}
	});

	annotator.annotator('addPlugin', 'Permissions', {
		user: current_user,
		permissions:{
			'read': 	[],
			'update': 	[current_user],
			'delete': 	[current_user],
			'admin': 	[current_user]
		},
		showViewPermissionsCheckbox: false,
		showEditPermissionsCheckbox: false
	});	
});