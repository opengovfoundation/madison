var DocumentPage = function() {
  var docInfo = element(by.css('.doc-info'));
  var commentsTabBtn = element(
    by.cssContainingText('.doc-content .nav-tabs a', 'Comments')
  );
  var documentTextTabBtn = element(
    by.cssContainingText('.doc-content .nav-tabs a', 'Document Text')
  );
  var tableOfContents = element(by.css('.toc'));

  var mainCommentForm = element(by.css('.comment-field:not(.ng-hide) > form'));
  var commentBox = mainCommentForm.element(by.css('#doc-comment-field'));;
  var commentBtn = mainCommentForm.element(by.cssContainingText('.btn', 'Add Comment'));

  function findTopLevelComment(text) {
    return element(by.cssContainingText('article.comment', text));
  }

  this.get = function() {
    browser.get('/docs/example-document');
  };

  this.info = {
    title: docInfo.element(by.css('.heading')),
    content: element(by.css('#content'))
  };

  this.stats = {
    participants: element(by.binding('doc.user_count')),
    comments: element(by.binding('doc.comment_count')),
    annotations: element(by.binding('doc.note_count')),

    updated: element(by.css('.stats-history .date')),

    supportCount: element(by.binding('doc.support')),
    supportChart: element(by.css('.support-chart .chart .support rect')),
    opposeCount: element(by.binding('doc.oppose')),
    opposeChart: element(by.css('.support-chart .chart .oppose rect'))
  };

  /**
   * Creating comments & replies
   */

  this.writeComment = function(text) {
    commentBox.sendKeys(text);
    commentBtn.click();
  };

  this.writeCommentReply = function(commentText, text) {
    findTopLevelComment(commentText)
      .element(by.css('textarea')).sendKeys(text);
    findTopLevelComment(commentText)
      .element(by.cssContainingText('.btn', 'Reply')).click();
    browser.sleep(1500);
  };

  this.findCommentThatMatches = function(text) {
    return findTopLevelComment(text);
  };

  /**
   * Liking comments
   */

  this.likeComment = function(text) {
    findTopLevelComment(text)
      .element(by.css('.activity-actions .thumbs-up')).click();
  };

  this.getCommentLikes = function(text) {
    return findTopLevelComment(text)
      .element(by.css('.activity-actions .thumbs-up'));
  };

  this.likeCommentReply = function(commentText, replyText) {
    findTopLevelComment(commentText).element(
      by.cssContainingText('.replies .comment-reply', replyText)
    ).element(by.css('.activity-actions .thumbs-up')).click();
  };

  this.getCommentReplyLikes = function(commentText, replyText) {
    return findTopLevelComment(commentText).element(
      by.cssContainingText('.replies .comment-reply', replyText)
    ).element(by.css('.activity-actions .thumbs-up'));
  };

  /**
   * Flagging comments
   */

  this.flagComment = function(text) {
    findTopLevelComment(text)
      .element(by.css('.activity-actions .flag')).click();
  };

  this.getCommentFlags = function(text) {
    return findTopLevelComment(text)
      .element(by.css('.activity-actions .flag'));
  };

  this.flagCommentReply = function(commentText, replyText) {
    findTopLevelComment(commentText).element(
      by.cssContainingText('.replies .comment-reply', replyText)
    ).element(by.css('.activity-actions .flag')).click();
  };

  this.getCommentReplyFlags = function(commentText, replyText) {
    return findTopLevelComment(commentText).element(
      by.cssContainingText('.replies .comment-reply', replyText)
    ).element(by.css('.activity-actions .flag'));
  };

  this.showCommentRepliesByText = function(commentText) {
    findTopLevelComment(commentText).element(
      by.css('.doc-replies-count')
    ).click();
  };

  this.showCommentRepliesByComment = function(comment) {
    comment.element.element(by.css('.doc-replies-count')).click();
  };

  this.findCommentReplyThatMatches = function(commentText, replyText) {
    return findTopLevelComment(commentText).element(
      by.cssContainingText('.replies .comment-reply', replyText)
    );
  };

  this.buttons = {
    support: element(by.css('#doc-support')),
    oppose: element(by.css('#doc-oppose'))
  };

  this.showComments = function() {
    commentsTabBtn.click();
  };

  this.showDocumentText = function() {
    documentTextTabBtn.click();
  };

  this.showTableOfContents = function() {
    element(by.css('.toc-title-side')).click();
    browser.driver.sleep(1000);
  };

  this.hideTableOfContents = function() {
    element(by.css('.toc-close')).click();
    browser.driver.sleep(1000);
  };

  this.getComment = function(row) {
    var comment = element(by.repeater('comment in doc.comments').row(row));

    return {
      element: comment,
      name: comment.element(by.css('.author')),
      body: comment.element(by.css('.content')),
      time: comment.element(by.css('.date'))
    };
  };

  this.getCommentReply = function(row) {
    var reply = element(by.repeater('reply in comment.comments').row(row));

    return {
      element: reply,
      name: reply.element(by.css('.author')),
      body: reply.element(by.css('.content')),
      time: reply.element(by.css('.date'))
    };
  };

  this.loginToCommentLink = function() {
    return element(
      by.cssContainingText('.comment-field:nth-child(2) a', 'Login to comment')
    );
  };

  this.pageWithTableOfContents = function() {
    return element(by.css('.single-doc.toc-open'));
  };

};

module.exports = DocumentPage;
