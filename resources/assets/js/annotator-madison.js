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

    /**
     *  Subscribe to Store's `annotationsLoaded` event
     *    Stores all annotation objects provided by Store in the window
     *    Adds all annotations to the sidebar
     **/
    this.annotator.subscribe('annotationsLoaded', function (annotations) {
      annotations.forEach(function (annotation) {
        this.processAnnotation(annotation);
      }.bind(this));

      // TODO: support scrolling to specific annotation

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
        this.addNoteLink(field, annotation);
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
        window.location.href = '/login';
      } else {
        this.onAdderClickOld(event);
      }
    }.bind(this);
  },

  processAnnotation: function (annotation) {
    annotation.highlights.forEach(function (highlight) {
      $(highlight).attr('id', 'annotation_' + annotation.id);
      $(highlight).attr('name', 'annotation_' + annotation.id);
    });

    annotation.commentsCollapsed = true;
    annotation.label = 'annotation';
    annotation.link = 'annotation_' + annotation.id;
    annotation.permalinkBase = 'annotation';
  },

  setAnnotations: function (annotations) {
    annotationGroups = this.groupAnnotations(annotations);
    this.annotations = annotations;
    this.annotationGroups = annotationGroups;
    this.drawNotesSideBubbles(annotations, annotationGroups);
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
    var userId = this.options.userId;

    // Add actions ( like / error) to annotation viewer
    var annotationAction = $('<div></div>').addClass('activity-actions');
    var generalAction = $('<span></span>').data('annotation-id', annotation.id);

    var annotationLike = generalAction.clone().addClass('thumbs-up').append('<span class="action-count">' + annotation.likes + '</span>');
    var annotationFlag = generalAction.clone().addClass('flag').append('<span class="action-count">' + annotation.flags + '</span>');

    annotationAction.append(annotationLike, annotationFlag);

    // If user is logged in add the current action and enable the action buttons
    if (userId) {
      if (annotation.user_action) {
        if (annotation.user_action === 'like') {
          annotationLike.addClass('selected');
        } else if (annotation.user_action === 'flag') {
          annotationFlag.addClass('selected');
        } // else this user doesn't have any actions on this annotation
      }

      var that = this;

      annotationLike.addClass('logged-in').click(function () {
        that.addLike(annotation, this);
      });

      annotationFlag.addClass('logged-in').click(function () {
        that.addFlag(annotation, this);
      });
    }

    $(field).append(annotationAction);
  },

  addNoteLink: function (field, annotation) {
    // Add link to annotation
    var linkPath = window.location.pathname + '#' + annotation.link;
    var annotationLink = $('<a></a>').attr('href', linkPath).text('Permanent link to this note').addClass('annotation-permalink');
    var noteLink = $('<div class="annotation-link"></div>');

    annotationLink.append(noteLink);
    $(field).append(annotationLink);
  },

  createComment: function (textElement, annotation) {
    var userId = this.options.userId;
    var docId = this.options.docId;
    var text = textElement.val();
    textElement.val('');

    var comment = {
      text: text,
      user: userId
    };

    // Add user's comment
    $.post('/documents/' + docId + '/comments/' + annotation.id + '/comments', comment, function () {
      annotation.comments.push(comment);

      return this.annotator.publish('commentCreated', comment);
    }.bind(this));
  },

  addLike: function (annotation, element) {
    var docId = this.options.docId;
    $.post('/documents/' + docId + '/comments/' + annotation.id + '/likes', function (data) {
      element = $(element);
      element.children('.action-count').text(data.likes);
      element.siblings('span').removeClass('selected');

      if (data.action) {
        element.addClass('selected');
      } else {
        element.removeClass('selected');
      }

      element.siblings('.thumbs-up').children('.action-count').text(data.likes);
      element.siblings('.flag').children('.action-count').text(data.flags);

      annotation.likes = data.likes;
      annotation.flags = data.flags;
      annotation.user_action = 'like';
    });
  },

  addFlag: function (annotation, element) {
    var docId = this.options.docId;
    $.post('/documents/' + docId + '/comments/' + annotation.id + '/flags', function (data) {
      element = $(element);
      element.children('.action-count').text(data.flags);
      element.siblings('span').removeClass('selected');

      if (data.action) {
        element.addClass('selected');
      } else {
        element.removeClass('selected');
      }

      element.siblings('.thumbs-up').children('.action-count').text(data.likes);

      annotation.likes = data.likes;
      annotation.flags = data.flags;
      annotation.user_action = 'flag';
    });
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

        sideBubbles += '<span class="annotation-group-count fa-stack">';
        sideBubbles += '<i class="fa fa-comment fa-stack-2x"></i>';
        sideBubbles += '<span class="fa-stack-1x">';
        sideBubbles += annotationGroup.annotations.length;
        sideBubbles += '</span>';
        sideBubbles += '</span>';

        sideBubbles += '<div class="annotation-group-statistics">';

        sideBubbles += '<span class="annotation-collaborator-count">';
        sideBubbles += window.trans['messages.document.collaborators_count']
          .replace(':count', annotationGroup.users.length);
        sideBubbles += '</span>';

        sideBubbles += '<span class="annotation-comment-count">';
        sideBubbles += window.trans['messages.document.replies_count']
          .replace(':count', annotationGroup.commentCount);
        sideBubbles += '</span>';

        sideBubbles += '</div>'; // annotation-group-statistics
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
      pane += '<article class="annotation">';

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

      // TODO: this
      // <div ng-hide="doc.discussion_state === 'closed'">
      //   <comment-actions object="annotation" root-target="doc"></comment-actions>
      //   <footer>
      //     <div class="reply-action">
      //       <a ng-click="showCommentForm($event)"
      //         translate="document.action.addreply"></a>
      //     </div>
      //   </footer>
      // </div>

      pane += '<section class="comments">';
      annotation.comments.forEach(function (comment) {
        pane += '<article class="comment"'
          + 'id="annsubcomment_'+comment.id+'"'
          + '>';

        pane += '<header class="comment-header">';
        pane += '<span class="author">'+comment.user.display_name+'</span>';
        pane += '<time class="date" datetime="'+comment.created_at+'">'+comment.created_at_relative+'</time>'
        pane += '</header>'

        pane += '<section class="content">';
        pane += comment.text;
        pane += '</section>';

        // TODO: discussion state
        // <div ng-hide="doc.discussion_state === 'closed'">
        // <comment-actions object="comment" root-target="doc"></comment-actions>
        // </div>
        pane += '</article>'; // comment
      });
      pane += '</section>'; // comments

      // TODO: this
      //   <section class="subcomment-form">
      //     <div ng-hide="doc.discussion_state === 'closed'">
      //       <form name="add-subcomment-form"
      //         ng-submit="subcommentSubmit(annotation, subcomment)" ng-if="user">
      //         <h4 translate="document.action.replynote"></h4>
      //         <input id="comment-form-field" ng-model="subcomment.text" type="text"
      //           class="form-control centered" required
      //           placeholder="{{ document.action.commentplaceholder | translate }}" />
      //         <button class="comment-button" type="submit"
      //           translate="document.action.postcomment"></button>
      //       </form>
      //     </div>
      // pane += '</section>'; // subcomment-form

        pane += '</article>';
    });

    pane += '</section>';
    pane += '</aside>';

    // insert new content
    $(this.options.annotationContainerElem).append(pane);
  },

  groupAnnotations: function (annotations) {
    var parentElements = 'h1,h2,h3,h4,h5,h6,li,p';
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
          top: positionTop,
          users: []
        };
      }

      // Count replies
      annotationGroups[annotationParentId].commentCount += annotation.comments.length;

      // Count the unique users for our annotations.
      if (annotationGroups[annotationParentId].users.indexOf(annotation.user.id) < 0) {
        annotationGroups[annotationParentId].users.push(annotation.user.id);
      }

      // Then count the unique users for the responses to each annotation.
      for (var commentIndex in annotation.comments) {
        var comment = annotation.comments[commentIndex];
        annotation.comments[commentIndex].permalinkBase ='annsubcomment';
        annotation.comments[commentIndex].label ='comment';
        if (annotationGroups[annotationParentId].users.indexOf(comment.user.id) < 0) {
          annotationGroups[annotationParentId].users.push(comment.user.id);
        }
      }

      annotationGroups[annotationParentId].annotations.push(annotation);
    }, this);

    return annotationGroups;
  }
});

window.hideNotes = function () {
  $('.annotation-click-capture').remove();
  $('.annotation-pane').removeClass('active');
};
