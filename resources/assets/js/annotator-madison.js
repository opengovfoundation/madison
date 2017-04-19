/*global Annotator*/
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

            let likeAction = { element: '', value: data.likes };
            let flagAction = { element: '', value: data.flags };

            if (action === 'likes') {
              likeAction.element = element;
              flagAction.element = element.siblings('.flag');
            } else {
              flagAction.element = element;
              likeAction.element = element.siblings('.thumbs-up');
            }

            // update live display
            likeAction.element.children('.action-count').text(likeAction.value);

            if (flagAction.value > 0) {
              flagAction.element.addClass('active');
            } else {
              flagAction.element.removeClass('active');
            }

            let flagCountElement = flagAction.element.children('.action-count');
            if (flagCountElement.length) {
              flagCountElement.text(flagAction.value);
            }

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

    // We want document events even on readOnly, which isn't the default
    // behavior.
    if (this.annotator.options.readOnly && !this.annotator.options.discussionClosed) {
      this.annotator._setupDocumentEvents();
    }

    // Disable viewing annotations through mouseover
    this.annotator.onHighlightMouseover = function (event) {
      return false;
    };

    // Clicking a highlight opens the panel for that annotation group
    $('.annotator-wrapper').delegate('.annotator-hl', 'click', function (event) {
      $(event.target).trigger('madison.showNotes');
    });

    // If the area outside of the editor is clicked, close it.
    this.annotator.subscribe('annotationEditorShown', function () {
      setTimeout(function() {
        $(document).one('click.annotationEditor', function (e) {
          let clickIsOutsideEditor = !$(e.target).closest('.annotator-editor').length;
          if (clickIsOutsideEditor) {
            $('.annotator-editor:not(.annotator-hide) .annotator-controls .annotator-cancel').trigger('click');
          }
        });
      }, 500);
    });

    // Just in case we close the editor with "cancel", clean up the single event binding
    this.annotator.subscribe('annotationEditorHidden', function () {
      $(document).off('click.annotationEditor');
    });

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
        annotationGroupCount = parseInt(annotationParentId.replace('annotationGroup-', ''));
      } else {
        annotationGroupCount++;
        annotationParentId = 'annotationGroup-' + annotationGroupCount;
        annotationParent.prop('id', annotationParentId);
      }

      // Set data-group-id on highlights so they can trigger notes pane
      annotation.highlights.forEach(function (highlight) {
        $(highlight).data('groupId', annotationParentId);
      });

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
