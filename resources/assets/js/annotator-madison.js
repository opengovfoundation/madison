/*global Annotator*/
/*global diff_match_patch*/
/*jslint newcap: true*/
Annotator.Plugin.Madison = function (element, options) {
  Annotator.Plugin.apply(this, arguments);
};

$.extend(Annotator.Plugin.Madison.prototype, new Annotator.Plugin(), {
  events: {},
  options: {},
  pluginInit: function () {
    $(document).on('madison.showNotes', function (e) {
      let annotationGroup = this.annotationGroups[$(e.target).data('groupId')];
      this.drawNotesPane(annotationGroup);
      window.setTimeout(function () {
        $('.annotation-pane').addClass('active');
      }, 100);
    }.bind(this));

    $(document).on('madison.addAction', function (e) {
      let annotationId = $(e.target).data('annotationId');
      let action = $(e.target).data('actionType');
      let element = $(e.target);
      let data = {
        _token: window.Laravel.csrfToken
      };

      if (this.options.userId) {
        $.post('/documents/' + this.options.docId + '/comments/' + annotationId + '/' + action, data)
          .done(function (data) {
            element = $(element);

            let otherAction = { element: '', value: 0 };
            let currentActionValue = 0;
            if (action === 'likes') {
              otherAction.element = '.flag';
              otherAction.value = data.flags;
              currentActionValue = data.likes;
            } else {
              otherAction.element = '.thumbs-up';
              otherAction.value = data.likes;
              currentActionValue = data.flags;
            }

            // update live display
            element.children('.action-count').text(currentActionValue);
            element.siblings(otherAction.element).children('.action-count').text(otherAction.value);

            // update data for later redrawing if needed
            let annotation = this.findAnnotation(annotationId);
            if (typeof annotation !== 'undefined') {
              annotation.likes = data.likes;
              annotation.flags = data.flags;
            }
          }.bind(this))
          .fail(function (data) {
            console.error(data);
          });
      } else {
        window.redirectToLogin();
      }
    }.bind(this));

    /**
     *  Subscribe to Store's `annotationsLoaded` event
     *    Stores all annotation objects provided by Store in the window
     *    Adds all annotations to the sidebar
     **/
    this.annotator.subscribe('annotationsLoaded', function (annotations) {
      annotations.forEach(function (annotation) {
        this.processAnnotation(annotation);
      }.bind(this));

      revealComment(this.options.docId);

      // TODO: support showing notes pane for requested permalink?

      this.setAnnotations(annotations);
    }.bind(this));

    /**
     *  Subscribe to Annotator's `annotationCreated` event
     *    Adds new annotation to the sidebar
     */
    this.annotator.subscribe('annotationCreated', function (annotation) {
      // TODO: show success notification or maybe in addAnnotation
      this.addAnnotation(annotation);
    }.bind(this));

    this.annotator.subscribe('commentCreated', function (comment) {
      comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.display_name + '</div></blockquote></div>');
      var currentComments = $('#current-comments');
      currentComments.append(comment);
      currentComments.removeClass('hidden');

      $('#current-comments').collapse(true);
    });

    this.annotator.subscribe('annotationViewerTextField', function (field, annotation) {
      if (annotation.tags.length === 0) {
        return;
      }

      annotation.tags.forEach(function (tag) {
        // TODO: support edits?
        // if (tag === 'edit') {
        //   var jField = $(field);
        //   var differ = new diff_match_patch();
        //   var diffs = differ.diff_main(annotation.quote, annotation.text);
        //   var html = differ.diff_prettyHtml(diffs);
        //   jField.find('p').html(html);
        // }
      });
    });

    // Add Madison-specific fields to the viewer when Annotator loads it
    this.annotator.viewer.addField({
      load: function (field, annotation) {
        this.addNoteActions(field, annotation);
        this.addComments(field, annotation);
      }.bind(this)
    });

    this.annotator.editor.submit = function (e) {
      // Clear previous errors
      this.annotation._error = false;

      var field, _i, _len, _ref;
      Annotator.Util.preventEventDefault(e);

      _ref = this.fields;

      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        field = _ref[_i];
        field.submit(field.element, this.annotation);
      }

      if (this.annotation._error !== true) {
        this.publish('save', [this.annotation]);

        return this.hide();
      }
    };

    this.annotator.editor.addField({
      load: function (field, annotation) {
        this.addEditFields(field, annotation);
      }.bind(this),
      submit: function (field, annotation) {
        // check it is tagged 'edit'
        if (this.hasEditTag(annotation.tags)) {
          // check we have explanatory content
          var explanation = $(field).find('#explanation').val();

          // If no explanatory content, show message and don't submit
          if ('' === explanation.trim()) {
            $('#annotation-error').text("Explanation required for edits.").toggle(true);

            annotation._error = true;
            return false;
          }

          annotation.explanation = explanation;
        }
      },
      hasEditTag: function (tags) {
        var hasEditTag = false;

        if (tags === undefined || tags.length  === 0) {
          return false;
        }

        tags.forEach(function (tag) {
          if (tag === 'edit') {
            hasEditTag = true;
          }
        });

        return hasEditTag;
      }
    });

    // We want document events even on readOnly, which isn't the default
    // behavior.
    if (this.annotator.options.readOnly && !this.annotator.options.discussionClosed) {
      this.annotator._setupDocumentEvents();
    }

    this.onAdderClickOld = this.annotator.onAdderClick;
    this.annotator.onAdderClick = function (event) {
      if (event !== null) {
        event.preventDefault();
      }

      if (!this.options.userId) {
        window.redirectToLogin();
      } else {
        this.onAdderClickOld(event);
      }
    }.bind(this);
  },

  processAnnotation: function (annotation) {
    annotation.highlights.forEach(function (highlight) {
      $(highlight).attr('id', annotation.htmlId);
    });

    annotation.comments.forEach(function (comment) {
      comment.htmlId = comment.id;
      comment.link = window.location.pathname+'#'+comment.htmlId;
    });

    annotation.commentsCollapsed = true;
    annotation.label = 'annotation';
    annotation.htmlId = annotation.id;
    annotation.link = window.location.pathname+'#'+annotation.htmlId;
  },

  setAnnotations: function (annotations) {
    annotationGroups = this.groupAnnotations(annotations);
    this.annotations = annotations;
    this.annotationGroups = annotationGroups;
    this.drawNotesSideBubbles(annotations, annotationGroups);
  },

  findAnnotation: function (annotationId) {
    for (var i = 0; i < this.annotations.length; i++) {
      let annotation = this.annotations[i];

      if (annotationId === annotation.id) {
        return annotation;
      }

      for (var j = 0; j < annotation.comments.length; j++) {
        let comment = annotation.comments[j];

        if (annotationId === comment.id) {
          return comment;
        }
      }
    }
  },

  addAnnotation: function (annotation) {
    if (annotation.id === undefined) {
      var interval = window.setInterval(function () {
        this.addAnnotation(annotation);
        window.clearInterval(interval);
      }.bind(this), 500);
    } else {
      this.processAnnotation(annotation);
      this.annotations.push(annotation);
      this.setAnnotations(this.annotations);
    }
  },

  addEditFields: function (field, annotation) {
    var newField = $(field);
    var toAdd = $('<div class="annotator-editor-edit-wrapper"></div>');

    var buttonGroup = $('<div class="btn-group"></div>');

    var explanation = $('<input id="explanation" type="text" name="explanation" placeholder="'+window.trans['messages.document.note_edit_explanation_prompt']+'" style="display:none;" />');
    var annotationError = $('<p id="annotation-error" style="display:none; color:red;"></p>');

    var annotateButton = $('<button type="button" class="btn btn-default active">'+window.trans['messages.document.note']+'</button>').click(function () {
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

    var editButton = $('<button type="button" class="btn btn-default">'+window.trans['messages.edit']+'</button>').click(function () {
      $(this).addClass('active');
      $(this).siblings().each(function (sibling) {
        $(this).removeClass('active');
      });
      $('#annotator-field-0').val(annotation.quote);
      $('#annotator-field-1').val('edit ');
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
    var userId = this.options.userId;

    // Add comment wrapper and collapse the comment thread
    var commentsHeader = $('<div class="comment-toggle" data-toggle-"collapse" data-target="#current-comments">Comments <span id="comment-caret" class="caret caret-right"></span></button>').click(function () {
      $('#current-comments').collapse('toggle');
      $('#comment-caret').toggleClass('caret-right');
    });

    // If there are no comments, hide the comment wrapper
    if ($(annotation.comments).length === 0) {
      commentsHeader.addClass('hidden');
    }

    // Add all current comments to the annotation viewer
    var currentComments = $('<div id="current-comments" class="current-comments collapse"></div>');

    /*jslint unparam: true*/
    $.each(annotation.comments, function (index, comment) {
      comment = $('<div class="existing-comment"><blockquote>' + comment.text + '<div class="comment-author">' + comment.user.display_name + '</div></blockquote></div>');
      currentComments.append(comment);
    });
    /*jslint unparam: false*/

    // Collapse the comment thread on load
    currentComments.ready(function () {
      $('#existing-comments').collapse({
        toggle: false
      });
    });

    // If the user is logged in, allow them to comment
    if (userId) {
      var annotationComments = $('<div class="annotation-comments"></div>');
      var commentText = $('<input type="text" class="form-control" />');
      var commentSubmit = $('<button type="button" class="btn btn-primary" >Submit</button>');
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
    $(field).append(this.noteActionsString(annotation));
  },

  noteActionsString: function (comment) {
    let actions = '<div class="activity-actions">';
    actions += '<a class="thumbs-up" onclick=$(this).trigger("madison.addAction")'
      + ' data-action-type="likes" data-annotation-id="'+comment.id+'"'
      + ' title="'+window.trans['messages.document.like']+'"'
      + ' aria-label="'+window.trans['messages.document.like']+'" role="button"'
      + ' ><span class="action-count">'+comment.likes+'</span></a>';

    actions += '<a class="flag" onclick=$(this).trigger("madison.addAction")'
      + ' data-action-type="flags" data-annotation-id="'+comment.id+'"'
      + ' title="'+window.trans['messages.document.flag']+'"'
      + ' aria-label="'+window.trans['messages.document.flag']+'" role="button"'
      + ' ><span class="action-count">'+comment.flags+'</span></a>';

    actions += '<a class="link" href="'+comment.link+'"'
      + ' aria-label="'+window.trans['messages.permalink']+'" role="button"'
      + ' title="'+window.trans['messages.permalink']+'">&nbsp;</a>';
    actions += '</div>';

    return actions;
  },

  createComment: function (textElement, annotation) {
    var userId = this.options.userId;
    var docId = this.options.docId;
    var text = textElement.val();
    textElement.val('');

    var comment = {
      text: text,
      user: userId,
      _token: window.Laravel.csrfToken
    };

    // Add user's comment
    $.post('/documents/' + docId + '/comments/' + annotation.id + '/comments', comment, function (commentResponse) {
      annotation.comments.push(commentResponse);

      return this.annotator.publish('commentCreated', commentResponse);
    }.bind(this));
  },

  drawNotesSideBubbles: function (annotations, annotationGroups) {
    // remove any existing content
    $('#participate-activity').remove();

    // draw annotation group side bubbles
    var sideBubbles = '<div id="participate-activity" class="participate-activity">';
    sideBubbles += '<div class="activity-thread">';

    if (annotations.length === 0) {
      sideBubbles += '<div>' + window.trans['messages.none'] + '</div>';
    } else {
      for (let index in annotationGroups) {
        let annotationGroup = annotationGroups[index];

        sideBubbles += '<div class="annotation-group"'
          + ' style="top:'+annotationGroup.top+'"'
          + ' onclick=$(this).trigger("madison.showNotes")'
          + ' data-group-id='+index+'>';

        sideBubbles += '<span class="annotation-group-count">';
        sideBubbles += '<i class="fa fa-comment fa-lg"></i>';
        sideBubbles += '<span class="badge">';
        sideBubbles += annotationGroup.annotations.length + annotationGroup.commentCount;
        sideBubbles += '</span>';
        sideBubbles += '</span>';

        sideBubbles += '</div>'; // annotation-group
      }
    }

    sideBubbles += '</div>';
    sideBubbles += '</div>';

    $(this.options.annotationContainerElem).append(sideBubbles);
  },

  drawNotesPane: function (annotationGroup) {
    // remove any existing content
    $('.annotation-pane').remove();

    // build up new content
    var pane = '';
    pane += '<aside class="annotation-pane">';

    pane += '<div class="annotation-click-capture" onclick="hideNotes()"></div>';

    pane += '<header class="title-header navbar navbar-default navbar-static-top">';
    pane += '<h2>'+window.trans['messages.document.notes']+'</h2>';
    pane += '<a class="close-button navbar-link" onclick="hideNotes()">';
    pane += window.trans['messages.close'];
    pane += '</a>';
    pane += '</header>';

    pane += '<section class="annotation-list">'
    annotationGroup.annotations.forEach(function (annotation) {
      pane += '<article class="annotation" id="' + annotation.htmlId + '">';

      pane += '<blockquote>&quot;';
      annotation.highlights.forEach(function (highlight) {
        pane += '<span>';
        pane += highlight.textContent;
        pane += '</span>';
      });
      pane += '&quot;</blockquote>';

      pane += '<div class="comment-body">'

      pane += '<header class="annotation-header">'
      pane += '<span class="author">'+annotation.user.display_name+'</span>';
      pane += '<time class="date" datetime="'+annotation.created_at+'">'+annotation.created_at_relative+'</time>'
      pane += '</header>';

      pane += '<section class="content">';
      pane += annotation.text;
      pane += '</section>';

      pane += '</div>'; // comment-body

      if (!this.annotator.options.discussionClosed) {
        pane += '<div>';
        pane += this.noteActionsString(annotation);
        pane += '<footer>';
        pane += '<div class="reply-action">';
        pane += '<a onclick="showNoteReplyForm('+this.options.userId+', \''+annotation.id+'\')">';
        pane += window.trans['messages.document.add_reply'];
        pane += '</a>';
        pane += '</div>';
        pane += '</footer>';
        pane += '</div>';
      }

      pane += '<section class="comments">';
      annotation.comments.forEach(function (comment) {
        pane += '<article class="comment"'
          + 'id="'+comment.htmlId+'"'
          + '>';

        pane += '<header class="comment-header">';
        pane += '<span class="author">'+comment.user.display_name+'</span>';
        pane += '<time class="date" datetime="'+comment.created_at+'">'+comment.created_at_relative+'</time>'
        pane += '</header>'

        pane += '<section class="content">';
        pane += comment.text;
        pane += '</section>';

        if (!this.annotator.options.discussionClosed) {
          pane += this.noteActionsString(comment);
        }

        pane += '</article>'; // comment
      }.bind(this));
      pane += '</section>'; // comments

      pane += '<section class="subcomment-form">';
      if (!this.annotator.options.discussionClosed && this.options.userId) {
        let url = '/documents/'+this.options.docId+'/comments/'+annotation.id+'/comments';
        pane += '<form name="add-subcomment-form" action="'+url+'" method="POST">';
        pane += '<h4>'+window.trans['messages.document.note_reply']+'</h4>';
        pane += '<input type="hidden" name="_token"' + ' value='+window.Laravel.csrfToken+' >';
        pane += '<textarea id="comment-form-field-'+annotation.id+'" name="text" class="form-control centered" required></textarea>';
        pane += '<button class="comment-button" type="submit">'+window.trans['messages.submit']+'</button>'; //TODO: trans
        pane += '</form>';
      }
      pane += '</section>'; // subcomment-form

      pane += '</article>';
    }.bind(this));

    pane += '</section>';
    pane += '</aside>';

    // insert new content
    $(this.options.annotationContainerElem).append(pane);
  },

  groupAnnotations: function (annotations) {
    var parentElements = 'h1,h2,h3,h4,h5,h6,li,p,tr,th,td';
    var annotationGroupCount = 0;
    var annotationGroups = [];

    annotations.forEach(function (annotation) {
      // Get the first highlight's parent, and show our toolbar link for it next
      // to it.
      var annotationParent = $(annotation.highlights[0]).parents(parentElements).first();

      if (annotationParent.length === 0) {
        return;
      }

      var annotationParentId;
      if (annotationParent.prop('id')) {
        annotationParentId = annotationParent.prop('id');
      } else {
        annotationGroupCount++;
        annotationParentId = 'annotationGroup-' + annotationGroupCount;
        annotationParent.prop('id', annotationParentId);
      }

      if ((typeof(annotationGroups[annotationParentId])).toLowerCase() === 'undefined') {
        var parentTop = annotationParent.offset().top;
        var containerTop = $(this.options.annotationContainerElem).offset().top;
        var positionTop = (parentTop - containerTop) + 'px';

        annotationGroups[annotationParentId] = {
          annotations: [],
          parent: annotationParent,
          parentId: annotationParentId,
          commentCount: 0,
          top: positionTop
        };
      }

      // Count replies
      annotationGroups[annotationParentId].commentCount += annotation.comments.length;

      annotationGroups[annotationParentId].annotations.push(annotation);
    }, this);

    return annotationGroups;
  }
});

window.hideNotes = function () {
  $('.annotation-click-capture').remove();
  $('.annotation-pane').removeClass('active');
};

window.showNoteReplyForm = function (userId, annotationId) {
  if (!userId) {
    window.redirectToLogin();
  }

  $('#comment-form-field-'+annotationId).focus();
};
