Annotator.Plugin.Madison = function(element, options){
	this.options = options;
}

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
	events: {},
	options: {},
	pluginInit: function(){
		this.annotator.viewer.addField({
			load: function(field, annotation){
				
				//Add link to annotation in Madison
				noteLink = $('<div class="annotation-link"></div>');
				annotationLink = $('<a></a>').attr('href', window.location.origin + '/note/' + annotation.id).text('View Note');
				noteLink.append(annotationLink);
				$(field).append(noteLink);

				current_user_id = $('#current_user_id').val();

				if(typeof current_user_id != 'undefined'){
					//Add actions to annotation
					annotationAction = $('<div></div>').addClass('annotation-action');
					generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);
					
					annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up');
					annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down');
					annotationFlag = generalAction.clone().addClass('glyphicon-flag');

					annotationAction.append(annotationLike, annotationDislike, annotationFlag);

					$(field).append(annotationAction);
				}
			}
		})
	}
});
	