window.loadAnnotations = function (contentElem, annotationContainerElem, docId, userId, discussionClosed) {
  var ann = $(contentElem).annotator({
    readOnly: !!userId,
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
      read: '/:id?only_notes=true',
      update: '/:id',
      destroy: '/:id',
      search: '/search'
    }
  });
};

window.toggleCommentReplies = function(commentId) {
  var $commentDiv = $('#' + commentId);
  var $commentReplyDiv = $commentDiv.find('.comment-replies');
  if ($commentReplyDiv) {
    $commentReplyDiv.toggleClass('hide');
  }
};

window.showCommentReplies = function(commentId) {
  var $commentDiv = $('#' + commentId);
  var $commentReplyDiv = $commentDiv.find('.comment-replies');
  if ($commentReplyDiv) {
    $commentReplyDiv.removeClass('hide');
  }
};

window.showComments = function () {
  $('a[href="#comments"]').tab('show');
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

    // look in comment pane for hash
    var comments = $('#comments').find('#'+id);
    if (comments.length) {
      showComments();
      comments[0].scrollIntoView();
      $('.anchor-target').removeClass('anchor-target');
      $(comments[0]).addClass('anchor-target');
      var parentComment = $(comments[0]).parents('.comment');
      if (parentComment.length) {
        showCommentReplies(parentComment[0].id);
      }
      return;
    }

    // look for highlight in the document content
    var noteHighlight = $('#content').find('[data-annotation-id='+id+']');
    if (noteHighlight.length) {
      noteHighlight[0].scrollIntoView();
      $('.anchor-target').removeClass('anchor-target');
      $(noteHighlight[0]).addClass('anchor-target');
      return;
    }

    // might be a reply to a note or on another page
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
