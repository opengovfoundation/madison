function addLike(annotation, element){
	$.post('/api/annotations/' + annotation.id + '/likes', function(data){
		element = $(element);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}
	});
}

function addDislike(annotation, element){
	$.post('/api/annotations/' + annotation.id + '/dislikes', function(data){
		element = $(element);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}
	});
}

function addFlag(annotation, element){
	$.post('/api/annotations/' + annotation.id + '/flags', function(data){
		element = $(element);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}
	});
}

Annotator.Plugin.Madison = function(element, options){
	this.options = options;
}

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
	events: {},
	options: {},
	pluginInit: function(){

		this.annotator.subscribe('annotationCreated', function(annotation){
			console.info("The annotation: %o has just been created!", annotation);
		});
		
		this.annotator.viewer.addField({
			load: function(field, annotation){

				//Add link to annotation in Madison
				noteLink = $('<div class="annotation-link"></div>');
				annotationLink = $('<a></a>').attr('href', window.location.origin + '/note/' + annotation.id).text('View Note');
				noteLink.append(annotationLink);
				$(field).append(noteLink);

				if(user.id != ''){
					//Add actions to annotation
					annotationAction = $('<div></div>').addClass('annotation-action');
					generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);
					
					annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up').click(function(){
						addLike(annotation, this);
					});

					annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down').click(function(){
						addDislike(annotation, this);
					});

					annotationFlag = generalAction.clone().addClass('glyphicon-flag').click(function(){
						addFlag(annotation, this);
					});

					if(annotation.user_action){
						if(annotation.user_action == 'like'){
							annotationLike.addClass('selected');
						}else if(annotation.user_action == 'dislike'){
							annotationDislike.addClass('selected');
						}else if(annotation.user_action == 'flag'){
							annotationFlag.addClass('selected');
						}else{
							console.error('User action not found.', annotation);
						}
					}
					

					annotationAction.append(annotationLike, annotationDislike, annotationFlag);

					$(field).append(annotationAction);
				}
			}
		});
	}
});
	