/*global Annotator*/
/*global getAnnotationService*/
/*global user*/
/*global doc*/
/*global diff_match_patch*/
Annotator.Plugin.Madison = function () {
  Annotator.Plugin.apply(this, arguments);
};

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
  events: {},
  options: {},
  pluginInit: function () {

    /**
     *  Subscribe to Store's `annotationsLoaded` event
     *    Stores all annotation objects provided by Store in the window
     *    Adds all annotations to the sidebar
     **/
    this.annotator.subscribe('annotationsLoaded', function (annotations) {
      annotations.forEach(function (annotation) {
        annotation.highlights.forEach(function (highlight) {
          $(highlight).attr('id', 'annotation_' + annotation.id);
          $(highlight).attr('name', 'annotation_' + annotation.id);
          annotation.link = 'annotation_' + annotation.id;
        });
      });

      //Set the annotations in the annotationService
      var annotationService = getAnnotationService();
      annotationService.setAnnotations(annotations);
    });

    /**
     *  Subscribe to Annotator's `annotationCreated` event
     *    Adds new annotation to the sidebar
     */
    this.annotator.subscribe('annotationCreated', function (annotation) {
      var annotationService = getAnnotationService();
      annotationService.addAnnotation(annotation);
      if ($.showAnnotationThanks) {
        $('#annotationThanks').modal({
          remote: '/modals/annotation_thanks',
          keyboard: true
        });
      }
    });

    this.annotator.subscribe('commentCreated', function (comment) {
      comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.name + '</div></blockquote></div>');
      var currentComments = $('#current-comments');
      currentComments.append(comment);
      currentComments.removeClass('hidden');

      $('#current-comments').collapse(true);
    });

    this.annotator.subscribe('annotationViewerTextField', function (field, annotation) {
      if(annotation.tags.length === 0){
        return;
      }

      var showDiff = false;

      annotation.tags.forEach(function (tag){
        if(tag === 'editar'){
          var jField = $(field);
          var differ = new diff_match_patch();
          var diffs = differ.diff_main(annotation.quote, annotation.text);
          var html = differ.diff_prettyHtml(diffs);
          jField.find('p').html(html);
        }
      });
    });

    //Add Madison-specific fields to the viewer when Annotator loads it
    this.annotator.viewer.addField({
      load: function (field, annotation) {
        this.addNoteLink(field, annotation);
        this.addNoteActions(field, annotation);
        this.addComments(field, annotation);
      }.bind(this)
    });

    this.annotator.editor.submit = function (e) {
      //Clear previous errors
      this.annotation._error = false;
      
      var field, _i, _len, _ref;
      Annotator.Util.preventEventDefault(e);

      _ref = this.fields;

      for (_i = 0, _len = _ref.length; _i < _len; _i++){
        field = _ref[_i];
        field.submit(field.element, this.annotation);
      }

      if(this.annotation._error !== true){
        this.publish('save', [this.annotation]);

        return this.hide();
      }
    };

    this.annotator.editor.addField({
      load: function (field, annotation) {
        this.addEditFields(field, annotation);
      }.bind(this),
      submit: function(field, annotation) {
        //check it is tagged 'edit'
        if(this.hasEditTag(annotation.tags)){
          //check we have explanatory content
          var explanation = $(field).find('#explanation').val();

          //If no explanatory content, show message and don't submit
          if('' == explanation.trim()){
            $('#annotation-error').text("Por favor explica por qué hiciste el cambio.").toggle(true);

            annotation._error = true;
            return false;
          }
          
          annotation.explanation = explanation;
        }
      },
      hasEditTag: function (tags) {
        var hasEditTag = false;

        if(tags === undefined || tags.length  === 0){
          return false;
        }

        tags.forEach(function (tag) {
          if (tag === 'editar') {
            hasEditTag = true;
          }
        });

        return hasEditTag;
      }
    });
  },
  addEditFields: function (field, annotation) {
    var newField = $(field);
    var toAdd = $('<div class="annotator-editor-edit-wrapper"></div>');

    var buttonGroup = $('<div class="btn-group"></div>');

    var explanation = $('<input id="explanation" type="text" name="explanation" placeholder="¿Por qué editaste esto?" style="display:none;" />');
    var annotationError = $('<p id="annotation-error" style="display:none; color:red;"></p>');

    var annotateButton = $('<button type="button" class="btn btn-default active">Anotar</button>').click(function () {
      $(this).addClass('active');
      $(this).siblings().each(function (sibling) {
        $(this).removeClass('active');
      });
      $('#annotator-field-0').val('');
      $('#annotator-field-1').val('');
      $('#explanation').toggle(false);
      $('#explanation').prop('required', false);
      $('#annotator-error').text('').toggle(false);
      $('#annotator-field-0').focus();
    });

    var editButton = $('<button type="button" class="btn btn-default">Editar</button>').click(function () {
      $(this).addClass('active');
      $(this).siblings().each(function (sibling) {
        $(this).removeClass('active');
      });
      $('#annotator-field-0').val(annotation.quote);
      $('#annotator-field-1').val('editar');
      $('#explanation').toggle(true);
      $('#explanation').prop('required', true);
      $('#annotator-field-0').focus();
    });

    buttonGroup.append(annotateButton, editButton);
    toAdd.append(buttonGroup);
    toAdd.append(explanation);
    toAdd.append(annotationError);
    newField.html(toAdd);
  },
  addComments: function (field, annotation) {
    //Add comment wrapper and collapse the comment thread
    var commentsHeader = $('<div class="comment-toggle" data-toggle-"collapse" data-target="#current-comments">Comentarios <span id="comment-caret" class="caret caret-right"></span></button>').click(function () {
      $('#current-comments').collapse('toggle');
      $('#comment-caret').toggleClass('caret-right');
    });

    //If there are no comments, hide the comment wrapper
    if ($(annotation.comments).length === 0) {
      commentsHeader.addClass('hidden');
    }

    //Add all current comments to the annotation viewer
    var currentComments = $('<div id="current-comments" class="current-comments collapse"></div>');

    /*jslint unparam: true*/
    $.each(annotation.comments, function (index, comment) {
      comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.name + '</div></blockquote></div>');
      currentComments.append(comment);
    });
    /*jslint unparam: false*/

    //Collapse the comment thread on load
    currentComments.ready(function () {
      $('#existing-comments').collapse({
        toggle: false
      });
    });

    //If the user is logged in, allow them to comment
    if (user.id !== '') {
      var annotationComments = $('<div class="annotation-comments"></div>');
      var commentText = $('<input type="text" class="form-control" />');
      var commentSubmit = $('<button type="button" class="btn btn-primary" >Enviar</button>');
      commentSubmit.click(function () {
        this.createComment(commentText, annotation);
      }.bind(this));
      annotationComments.append(commentText);

      annotationComments.append(commentSubmit);

      $(field).append(annotationComments);
    }

    $(field).append(commentsHeader, currentComments);
  },
  addNoteActions: function (field, annotation) {
    //Add actions ( like / dislike / error) to annotation viewer
    var annotationAction = $('<div></div>').addClass('annotation-action');
    var generalAction = $('<span></span>').addClass('glyphicon').data('annotation-id', annotation.id);

    var annotationLike = generalAction.clone().addClass('glyphicon-thumbs-up').append('<span class="action-count">' + annotation.likes + '</span>');
    var annotationDislike = generalAction.clone().addClass('glyphicon-thumbs-down').append('<span class="action-count">' + annotation.dislikes + '</span>');
    var annotationFlag = generalAction.clone().addClass('glyphicon-flag').append('<span class="action-count">' + annotation.flags + '</span>');

    annotationAction.append(annotationLike, annotationDislike, annotationFlag);

    //If user is logged in add his current action and enable the action buttons
    if (user.id !== '') {
      if (annotation.user_action) {
        if (annotation.user_action === 'like') {
          annotationLike.addClass('selected');
        } else if (annotation.user_action === 'dislike') {
          annotationDislike.addClass('selected');
        } else if (annotation.user_action === 'flag') {
          annotationFlag.addClass('selected');
        } // else this user doesn't have any actions on this annotation
      }

      var that = this;

      annotationLike.addClass('logged-in').click(function () {
        that.addLike(annotation, this);
      });

      annotationDislike.addClass('logged-in').click(function () {
        that.addDislike(annotation, this);
      });

      annotationFlag.addClass('logged-in').click(function () {
        that.addFlag(annotation, this);
      });
    }

    $(field).append(annotationAction);
  },
  addNoteLink: function (field, annotation) {
    //Add link to annotation
    var noteLink = $('<div class="annotation-link"></div>');
    var linkPath = window.location.origin + window.location.pathname + '#' + annotation.link;
    var annotationLink = $('<a></a>').attr('href', window.location.pathname + '#' + annotation.link).text('Copiar Enlace de Anotación').addClass('annotation-permalink');
    annotationLink.attr('data-clipboard-text', linkPath);

    var client = new ZeroClipboard(annotationLink);

    noteLink.append(annotationLink);
    $(field).append(noteLink);
  },
  createComment: function (textElement, annotation) {
    var text = textElement.val();
    textElement.val('');

    var comment = {
      text: text,
      user: user
    };

    //POST request to add user's comment
    $.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/comments', {
      comment: comment
    }, function () {
      annotation.comments.push(comment);

      return this.annotator.publish('commentCreated', comment);
    }.bind(this));
  },
  addLike: function (annotation, element) {
    $.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/likes', function (data) {
      element = $(element);
      element.children('.action-count').text(data.likes);
      element.siblings('.glyphicon').removeClass('selected');

      if (data.action) {
        element.addClass('selected');
      } else {
        element.removeClass('selected');
      }

      element.siblings('.glyphicon-thumbs-up').children('.action-count').text(data.likes);
      element.siblings('.glyphicon-thumbs-down').children('.action-count').text(data.dislikes);
      element.siblings('.glyphicon-flag').children('.action-count').text(data.flags);

      annotation.likes = data.likes;
      annotation.dislikes = data.dislikes;
      annotation.flags = data.flags;
      annotation.user_action = 'like';
    });
  },
  addDislike: function (annotation, element) {
    $.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/dislikes', function (data) {
      element = $(element);
      element.children('.action-count').text(data.dislikes);
      element.siblings('.glyphicon').removeClass('selected');

      if (data.action) {
        element.addClass('selected');
      } else {
        element.removeClass('selected');
      }

      element.siblings('.glyphicon-thumbs-up').children('.action-count').text(data.likes);
      element.siblings('.glyphicon-thumbs-down').children('.action-count').text(data.dislikes);
      element.siblings('.glyphicon-flag').children('.action-count').text(data.flags);

      annotation.likes = data.likes;
      annotation.dislikes = data.dislikes;
      annotation.flags = data.flags;
      annotation.user_action = 'dislike';
    });
  },
  addFlag: function (annotation, element) {
    $.post('/api/docs/' + doc.id + '/annotations/' + annotation.id + '/flags', function (data) {
      element = $(element);
      element.children('.action-count').text(data.flags);
      element.siblings('.glyphicon').removeClass('selected');

      if (data.action) {
        element.addClass('selected');
      } else {
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
});
