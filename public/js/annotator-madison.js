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
	Annotator.Plugin.apply(this, arguments);
}

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
	events: {},
	options: {},
	pluginInit: function(){

		//Subscribe to the annotationsLoaded call.  This allows us to grab all annotation objects from the Store plugin for our purposes
		this.annotator.subscribe('annotationsLoaded', function(annotations){
			window.annotations = annotations;

			try{
				this.copyNotesToSidebar(annotations);	
			}catch(e){
				console.error(e);
			}
		}.bind(this));

		//Subscribe to the annotationCreated call.  This allows us to grab annotations as they're created
		this.annotator.subscribe('annotationCreated', function(annotation){
			var converter = new Markdown.Converter();

			//Append new annotations to the sidebar
			sidebarNote = $('<a href="/note/' + annotation.id + '"><div class="sidebar-annotation"><blockquote>' + converter.makeHtml(annotation.text) + '<div class="annotation-author">' + annotation.user.name + '</div></blockquote></div></a>');
			$('#participate-notes').append(sidebarNote);
		});

		//Add Madison-specific fields to the viewer when Annotator loads it
		this.annotator.viewer.addField({
			load: function(field, annotation){

				//Add link to annotation
				noteLink = $('<div class="annotation-link"></div>');
				annotationLink = $('<a></a>').attr('href', window.location.origin + '/note/' + annotation.id).text('View Note');
				noteLink.append(annotationLink);
				$(field).append(noteLink);

				//Add actions ( like / dislike / error) to annotation viewer
				annotationAction = $('<div></div>').addClass('annotation-action');
				generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);
				
				annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up').append('<span class="action-count">' + annotation.likes + '</span>');
				annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down').append('<span class="action-count">' + annotation.dislikes + '</span>');
				annotationFlag = generalAction.clone().addClass('glyphicon-flag').append('<span class="action-count">' + annotation.flags + '</span>');

				annotationAction.append(annotationLike, annotationDislike, annotationFlag);

				//If user is logged in add his current action and enable the action buttons
				if(user.id != ''){
					if(annotation.user_action){
						if(annotation.user_action == 'like'){
							annotationLike.addClass('selected');
						}else if(annotation.user_action == 'dislike'){
							annotationDislike.addClass('selected');
						}else if(annotation.user_action == 'flag'){
							annotationFlag.addClass('selected');
						}else{
							// This user doesn't have any actions on this annotation
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

				//Add comment wrapper and collapse the comment thread
				commentsHeader = $('<div class="comment-toggle" data-toggle-"collapse" data-target="#current-comments">Comments <span id="comment-caret" class="caret caret-right"></span></button>').click(function(){
					$('#current-comments').collapse('toggle');
					$('#comment-caret').toggleClass('caret-right');
				});

				//If there are no comments, hide the comment wrapper
				if($(annotation.comments).length == 0){
					commentsHeader.addClass('hidden');
				}

				//Add all current comments to the annotation viewer
				currentComments = $('<div id="current-comments" class="current-comments collapse"></div>');
				$.each(annotation.comments, function(index, comment){
					comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.name + '</div></blockquote></div>');
					currentComments.append(comment);
				});

				//Collapse the comment thread on load
				currentComments.ready(function(){
					$('#existing-comments').collapse({toggle: false});
				});

				//If the user is logged in, allow them to comment
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

						//POST request to add user's comment
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
	},
	copyNotesToSidebar: function(annotations){
		//Append each loaded annotation to the sidebar
		sidebarNotes = $('#participate-notes');

		$.each(annotations, function(index, annotation){
			this.copyNoteToSidebar(annotation, sidebarNotes);
		}.bind(this));
	},
	copyNoteToSidebar: function(annotation, sidebarNotes){
		var converter = new Markdown.Converter();

		sidebarNote = $('<a href="/note/' + annotation.id + '"><div class="sidebar-annotation"><blockquote>' + converter.makeHtml(annotation.text) + '<div class="annotation-author">' + annotation.user.name + '</div></blockquote></div></a>');
		sidebarNotes.append(sidebarNote);
	}
});
	