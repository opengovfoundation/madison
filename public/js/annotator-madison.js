Annotator.Plugin.Madison = function(element){
	var madison = {};

	madison.pluginInit = function(){
		this.annotator.viewer.addField({
			load: function(field, annotation){

				//Add link to note in Madison
				noteLink = $('<div class="annotation-link"></div>');
				annotationLink = $('<a></a>').attr('href', window.location.origin + '/note/' + annotation.id).text('View Note');
				noteLink.append(annotationLink);

				$(field).append(noteLink);
				annotationAction = $('<div></div>').addClass('annotation-action');
				generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);
				annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up');
				annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down');
				annotationFlag = generalAction.clone().addClass('glyphicon-flag');

				annotationAction.append(annotationLike, annotationDislike, annotationFlag);

				$(field).append(annotationAction);
			}
		})
	}

	return madison;
}