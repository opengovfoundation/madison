$(document).ready(function(){
	var annotator = $('#content').annotator({
		readOnly: user.id == ''
	});

	annotator.annotator('addPlugin', 'Unsupported');
	annotator.annotator('addPlugin', 'Tags');
	annotator.annotator('addPlugin', 'Markdown');
	annotator.annotator('addPlugin', 'Store', {
		annotationData:{
			'uri': window.location.href,
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
		user: user,
		permissions:{
			'read': 	[],
			'update': 	[user.id],
			'delete': 	[user.id],
			'admin': 	[user.id]
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

	annotator.annotator('addPlugin', 'Madison', {
		userId: user.id
	});
});