$(document).ready(function(){
	var current_user = $('#current_user').val();
	var current_user_id = parseInt($('#current_user_id').val());
	var current_user_name = $('#current_user_name').val();

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
		user: { 
			'id': 	current_user_id,
			'name': current_user_name
		},
		permissions:{
			'read': 	[],
			'update': 	[current_user_id],
			'delete': 	[current_user_id],
			'admin': 	[current_user_id]
		},
		showViewPermissionsCheckbox: false,
		showEditPermissionsCheckbox: false,
		userId: function(user){
			if(user && user.id){
				return user.id;
			}

			return user;
		},
		userString: function(user){
			if(user && user.name){
				return user.name;
			}
			
			return user;
		}
	});

	annotator.annotator('addPlugin', 'Madison');
});