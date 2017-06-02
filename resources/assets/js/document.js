window.loadAnnotations = function (contentElem, annotationContainerElem, docId, userId, discussionClosed) {
  var ann = $(contentElem).annotator({
    readOnly: !!!userId,
    discussionClosed: discussionClosed
  });
  ann.annotator('addPlugin', 'Unsupported');
  ann.annotator('addPlugin', 'Madison', {
    docId: docId,
    userId: userId,
    annotationContainerElem: annotationContainerElem
  });
  ann.annotator('addPlugin', 'Store', {
    annotationData: {
      '_token': window.Laravel.csrfToken,
      'uri': window.location.pathname,
      'comments': []
    },
    prefix: '/documents/' + docId + '/comments',
    urls: {
      create: '',
      read: '/:id?only_notes=true&include_replies=false&include_content=false',
      update: '/:id',
      destroy: '/:id',
      search: '/search'
    }
  });
};

window.toggleCommentReplies = function(toggleBtn) {
  var $commentReplyDiv = toggleBtn.parents('.comment').first().find('.comment-replies').first();
  if ($commentReplyDiv) {
    $commentReplyDiv.toggleClass('hidden');
  }
};

window.showCommentReplies = function(commentId) {
  var $commentDiv = $('#' + commentId);
  var $commentReplyDiv = $commentDiv.find('.comment-replies');
  if ($commentReplyDiv) {
    $commentReplyDiv.removeClass('hidden');
  }
};

window.showComments = function () {
  $('#comments')[0].scrollIntoView();
};

window.anchorToHighlight = function (id) {
  var noteHighlight = $('#page_content').find('.annotator-hl[data-annotation-id='+id+']');
  if (noteHighlight.length) {
    noteHighlight[0].scrollIntoView();
    $('.anchor-target').removeClass('anchor-target');
    $(noteHighlight[0]).addClass('anchor-target');
    return;
  }
};

window.revealComment = function (docId) {
  var noteReplyHash = window.location.hash.match(/^#annsubcomment_([0-9]+)-?([0-9]+)?$/);
  var noteHash = window.location.hash.match(/^#annotation_([0-9]+)$/);
  var commentHash = window.location.hash.match(/^#comment_([0-9]+)-?([0-9]+)?$/);
  var hash = jQuery.Deferred();
  var lookupNewId = function (oldId) {
    return $.get('/documents/'+docId+'/comments/'+oldId, null, null, 'json')
       .done(function (data) {
         hash.resolve(data.id);
       });
  };

  hash.done(function (id) {
    if (!id) {
      return;
    }

    // if note pane is open with content, then we should look there first
    var note = $('.annotation-container').find('#'+id);
    if (note.length) {
      note[0].scrollIntoView();
      $('.anchor-target').removeClass('anchor-target');
      $(note[0]).find('.comment-content').first().addClass('anchor-target');
      return;
    }

    // look in comments
    var comments = $('#comments').find('#'+id);
    if (comments.length) {
      showComments();
      comments[0].scrollIntoView();
      $('.anchor-target').removeClass('anchor-target');
      $(comments[0]).find('.comment-content').first().addClass('anchor-target');
      var parentComment = $(comments[0]).parents('.comment');
      if (parentComment.length) {
        showCommentReplies(parentComment[0].id);
      }
      return;
    }

    // look for highlight in the document content
    var noteHighlight = $('#page_content').find('.annotator-hl[data-annotation-id='+id+']');
    if (noteHighlight.length) {
      noteHighlight[0].scrollIntoView();
      $('.anchor-target').removeClass('anchor-target');
      $(noteHighlight[0]).addClass('anchor-target');
      return;
    }
  });

  if (commentHash) {
    if (commentHash[2]) {
      lookupNewId(commentHash[2]);
    } else {
      lookupNewId(commentHash[1]);
    }
  } else if (noteHash) {
    lookupNewId(noteHash[1]);
  } else if (noteReplyHash) {
    if (noteReplyHash[2]) {
      lookupNewId(noteReplyHash[2]);
    } else {
      lookupNewId(noteReplyHash[1]);
    }
  } else {
    hash.resolve(window.location.hash.slice(1));
  }
};

window.toggleNewCommentForm = function (elem) {
  var parents = $(elem).parents('.new-comment-form');
  if (parents.length) {
    var $collapsedContent = $(parents[0]).find('.collapsed-content');
    var $expandedContent = $(parents[0]).find('.expanded-content');

    $collapsedContent.toggleClass('hidden');
    $expandedContent.toggleClass('hidden');

    if (!$expandedContent.hasClass('hidden')) {
      $expandedContent.find(':input').filter(':visible:first').focus();
    }
  }
};

window.submitNewComment = function (e) {
  e.preventDefault();

  var $form = $(e.target);
  var $comment = $form.parents('.comment').first();
  var $comments = $form.parents('.comments').first().find('.media-list').first();

  // we are replying to an existing comment
  if ($comment.length) {
    $comment.addClass('pending');

    // submit comment
    $.post($form.attr('action'), $form.serialize())
      .done(function (response) {
        // if success, fetch new markup and swap with existing
        $.get('/documents/'+window.documentId+'/comments/'+$comment.attr('id'), { 'partial': true }, null, "html")
          .done(function (html) {
            $comment.replaceWith(html);

            // highlight new comment
            window.location.hash = '#' + response.id;
          });
      })
      .fail(function (response) {
        // TODO: if error, show error
      })
      .always(function () {
        $comment.removeClass('pending');
      })
    ;
  } else if ($comments.length) {
    // we are adding a new top level comment
    $form.addClass('pending');

    // submit comment
    $.post($form.attr('action'), $form.serialize())
      .done(function (response) {
        $form.trigger('reset');
        toggleNewCommentForm($form);

        $.get('/documents/'+window.documentId+'/comments/'+response.id, { 'partial': true }, null, "html")
          .done(function (html) {
            $comments.prepend(html);

            // highlight new comment
            window.location.hash = '#' + response.id;
          });
      })
      .fail(function (response) {
        // TODO: if error, show error
      })
      .always(function () {
        $form.removeClass('pending');
      })
    ;
  }


  return false;
};

window.buildDocumentOutline = function (outlineContainer, documentContent) {
  var contentHeadings = $(documentContent).find('h1,h2,h3,h4,h5,h6').toArray();
  var outlineTree = contentHeadings.reduce(buildOutlineTree, []);
  var $affixedOutlineList = $(outlineContainer).children('ul');

  // Create HTML from the outline tree and put into container
  $(outlineContainer).find('ul').append(outlineTree.reduce(buildItemHtml, ''));

  // Set the outline to be affixed
  $(outlineContainer).children('ul').affix({
    offset: {
      top: $(outlineContainer).offset().top - 15,
      bottom: function () {
        return $(document).height() - ($('#page_content').offset().top + $('#page_content').height());
      }
    }
  });

  $(window).scroll(function (e) {
    if (!$affixedOutlineList.hasClass('affix')) return;

    // get current position in #page_content as a %
    var topOfContent = $(documentContent).offset().top;
    var contentHeight = $(documentContent).height();
    var contentPosition = $(document).scrollTop() - topOfContent;
    var contentPositionDecimal = (contentPosition / contentHeight);

    // get equivalent percentage position in outline content
    var outerOutlineHeight = $affixedOutlineList.height();
    var innerOutlineHeight = $affixedOutlineList[0].scrollHeight;
    var outlineScrollPosition = (innerOutlineHeight * contentPositionDecimal);

    // set the scroll to that position
    $affixedOutlineList.scrollTop(outlineScrollPosition - (outerOutlineHeight / 2));
  });

  // Set scrollspy on the outline
  $('body').scrollspy({ target: outlineContainer });

  function buildOutlineTree (outlineTree, heading, idx) {
    var lastTopLevelHeading = outlineTree[outlineTree.length - 1];

    var newHeading = {
      el: heading,
      level: parseInt(heading.tagName[1]),
      id: 'toc-heading-' + idx,
      text: $(heading).text(),
      subHeadings: []
    };

    if (!lastTopLevelHeading || newHeading.level <= lastTopLevelHeading.level) {
      outlineTree.push(newHeading);
    } else {
      outlineTree[outlineTree.length - 1].subHeadings.push(newHeading);
    }

    return outlineTree;
  }

  function buildItemHtml (html, item) {
    html += '<li><a href="#' + item.id + '">' + item.text + '</a>';

    if (item.subHeadings.length > 0) {
      html += '<ul class="nav">';
      html += item.subHeadings.reduce(buildItemHtml, '');
      html += '</ul>';
    }

    html += '</li>';

    // Side effect! Attach ID to the heading element
    $(item.el).attr('id', item.id);

    return html;
  }
};
