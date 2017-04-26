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

      // remove any existing content
      $(this.options.annotationContainerElem).find('.annotation-list').empty().addClass('pending');

      // start showing the pane
      $('.annotation-pane').addClass('active');
      $('.annotation-click-capture').removeClass('hidden');

      this.loadNotesPane(annotationGroup);
    }.bind(this));

    $(document).on('madison.addAction', function (e) {
      let annotationId = $(e.target).data('annotationId');
      let action = $(e.target).data('actionType');
      let sourceElement = $(e.target);
      let data = {
        _token: window.Laravel.csrfToken
      };

      if (this.options.userId) {
        $.post('/documents/' + this.options.docId + '/comments/' + annotationId + '/' + action, data)
          .done(function (data) {
            sourceElement = $(sourceElement);

            let likeAction = { element: '', value: data.likes };
            let flagAction = { element: '', value: data.flags };

            if (action === 'likes') {
              likeAction.element = sourceElement;
              flagAction.element = sourceElement.siblings('.flag');
            } else {
              flagAction.element = sourceElement;
              likeAction.element = sourceElement.siblings('.thumbs-up');
            }

            // update live display
            likeAction.element.children('.action-count').text(likeAction.value);

            if (flagAction.value > 0) {
              flagAction.element.addClass('active');
            } else {
              flagAction.element.removeClass('active');
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

  drawNotesSideBubbles: function (annotations, annotationGroups) {
    // remove any existing content
    $('#participate-activity').remove();

    // draw annotation group side bubbles
    var sideBubbles = '<div id="participate-activity" class="participate-activity">';
    sideBubbles += '<div class="activity-thread">';

    if (annotations.length !== 0) {
      for (let index in annotationGroups) {
        let annotationGroup = annotationGroups[index];

        sideBubbles += '<div class="annotation-group"'
          + ' style="top:'+annotationGroup.top+'"'
          + ' onclick=$(this).trigger("madison.showNotes")'
          + ' data-group-id='+index+'>';

        sideBubbles += '<span class="annotation-group-count">';
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

  loadNotesPane: function (annotationGroup) {
    // build up new content
    return $.get('/documents/'+this.options.docId+'/comments/',
          {'partial': true,
           'ids': annotationGroup.annotations.map(function (ann) { return ann.id; })
          }, null, "html")
      .done(function (data) {
        $(this.options.annotationContainerElem).find('.annotation-list').html(data).removeClass('pending');
      }.bind(this));
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
      annotationGroups[annotationParentId].commentCount += annotation.comments_count;

      annotationGroups[annotationParentId].annotations.push(annotation);
    }, this);

    return annotationGroups;
  }
});

window.hideNotes = function () {
  $('.annotation-click-capture').addClass('hidden');
  $('.annotation-pane').removeClass('active');
};
