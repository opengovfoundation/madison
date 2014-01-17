function addLike(annotation, element){
	$.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/likes', function(data){
		element = $(element);
		element.children('.action-count').text(data.likes);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}

		element.siblings('.glyphicon-thumbs-down').children('.action-count').text(data.dislikes);
		element.siblings('.glyphicon-flag').children('.action-count').text(data.flags);

		annotation.likes = data.likes;
		annotation.dislikes = data.dislikes;
		annotation.flags = data.flags;
		annotation.user_action = 'like';
	});
}

function addDislike(annotation, element){
	$.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/dislikes', function(data){
		element = $(element);
		element.children('.action-count').text(data.dislikes);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}

		element.siblings('.glyphicon-thumbs-up').children('.action-count').text(data.likes);
		element.siblings('.glyphicon-flag').children('.action-count').text(data.flags);

		annotation.likes = data.likes;
		annotation.dislikes = data.dislikes;
		annotation.flags = data.flags;
		annotation.user_action = 'dislike';
	});
}

function addFlag(annotation, element){
	$.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/flags', function(data){
		element = $(element);
		element.children('.action-count').text(data.flags);
		element.siblings('.glyphicon').removeClass('selected');

		if(data.action){
			element.addClass('selected');
		}else{
			element.removeClass('selected');
		}

		element.siblings('.glyphicon-thumbs-up').children('.action-count').text(data.likes);
		element.siblings('.glyphicon-thumbs-down ').children('.action-count').text(data.dislikes);

		annotation.likes = data.likes;
		annotation.dislikes = data.dislikes;
		annotation.flags = data.flags;
		annotation.user_action = 'flag';
	});
}

Annotator.Plugin.Madison = function(element, options){
	this.options = options;
}

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
	events: {},
	options: {},
	pluginInit: function(){
		this.annotator.subscribe('annotationsLoaded', function(annotations){
			window.annotations = annotations;

			sidebarNotes = $('#participate-notes');

			$.each(annotations, function(index, annotation){
				sidebarNote = $('<div class="sidebar-annotation"><blockquote>' + annotation.text + '<div class="annotation-author">' + annotation.user.name + '</div></blockquote></div>');
				sidebarNotes.append(sidebarNote);
			});
		});

		this.annotator.subscribe('annotationCreated', function(annotation){
			sidebarNote = $('<div class="sidebar-annotation"><blockquote>' + annotation.text + '<div class="annotation-author">' + annotation.user.name + '</div></blockquote></div>');
			$('#participate-notes').append(sidebarNote);
		});

		this.annotator.viewer.addField({
			load: function(field, annotation){

				//Add link to annotation in Madison
				noteLink = $('<div class="annotation-link"></div>');
				annotationLink = $('<a></a>').attr('href', window.location.origin + '/note/' + annotation.id).text('View Note');
				noteLink.append(annotationLink);
				$(field).append(noteLink);

				//Add actions to annotation
				annotationAction = $('<div></div>').addClass('annotation-action');
				generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);
				
				annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up').append('<span class="action-count">' + annotation.likes + '</span>');
				annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down').append('<span class="action-count">' + annotation.dislikes + '</span>');
				annotationFlag = generalAction.clone().addClass('glyphicon-flag').append('<span class="action-count">' + annotation.flags + '</span>');

				annotationAction.append(annotationLike, annotationDislike, annotationFlag);

				if(user.id != ''){
					
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

					annotationLike.addClass('logged-in').click(function(){
						addLike(annotation, this);
					});

					annotationDislike.addClass('logged-in').click(function(){
						addDislike(annotation, this);
					});

					annotationFlag.addClass('logged-in').click(function(){
						addFlag(annotation, this);
					});
				}

				$(field).append(annotationAction);

				commentsHeader = $('<div class="comment-toggle" data-toggle-"collapse" data-target="#current-comments">Comments <span id="comment-caret" class="caret caret-right"></span></button>').click(function(){
					$('#current-comments').collapse('toggle');
					$('#comment-caret').toggleClass('caret-right');
				});

				if($(annotation.comments).length == 0){
					commentsHeader.addClass('hidden');
				}

				currentComments = $('<div id="current-comments" class="current-comments collapse"></div>');
				$.each(annotation.comments, function(index, comment){
					comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.name + '</div></blockquote></div>');
					currentComments.append(comment);
				});

				currentComments.ready(function(){
					$('#existing-comments').collapse({toggle: false});
				});

				if(user.id != ''){
					annotationComments = $('<div class="annotation-comments"></div>');
					annotationComments.append('<input type="text" class="form-control" />');

					annotationComments.append($('<button type="button" class="btn btn-primary" >Submit</button>').click(function(){
						text = $(this).parent().children('input[type="text"]').val();
						$(this).parent().children('input[type="text"]').val('');
						
						comment = {
							text: text,
							user: user
						}

						$.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/comments', {comment: comment}, function(response){
							annotation.comments.push(comment);
							comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.name + '</div></blockquote></div>');
							currentComments.append(comment);
							currentComments.removeClass('hidden');
						});

						$('#current-comments').collapse(true);
					}));

					$(field).append(annotationComments);
				}

				$(field).append(commentsHeader, currentComments);			
			}
		});
	}
});
	