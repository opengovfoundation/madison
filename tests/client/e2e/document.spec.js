describe('Document view', function() {
  beforeEach(function() {
    browser.get('/docs/example-document');
  });

  it('shows the document specified by the URL', function() {
    var docInfo = element(by.css('.doc-info'));
    var docTitle = docInfo.element(by.css('.heading'));
    expect(docTitle.getText()).toBe('Example Document');
  });

  it('shows the bill text', function() {
    var docContent = element(by.css('#content'));
    expect(docContent.getText()).toMatch(/New Document Content/);
  });

  describe('Document stats', function() {
    it('shows the number of participants', function() {
      var participantCount = element(by.binding('doc.user_count'));
      expect(participantCount.getText()).toBe('2');
    });

    it('shows the number of comments', function() {
      var commentCount = element(by.binding('doc.comment_count'));
      expect(commentCount.getText()).toBe('3');
    });

    it('shows the number of annotations', function() {
      var annotationCount = element(by.binding('doc.annotation_count'));
      expect(annotationCount.getText()).toBe('2');
    });

    it('shows how recently the bill was updated', function() {
      var updatedAt = element(by.css('.stats-history .date'));
      expect(updatedAt.getText()).toMatch(/updated about \d+ .+ ago/i);
    });

    it('shows support and opposition', function() {
      var supportCount = element(by.binding('doc.support'));
      var supportChart = element(by.css('.support-chart .chart .support rect'));
      var opposeCount = element(by.binding('doc.oppose'));
      var supportChart = element(by.css('.support-chart .chart .oppose rect'));

      expect(supportCount.getText()).toBe('0');
      expect(supportChart.getAttribute('width')).toBe('0');

      expect(opposeCount.getText()).toBe('0');
      expect(supportChart.getAttribute('width')).toBe('0');
    });
  });

  describe('Document comments', function() {
    beforeEach(function() {
      // Click the discussion tab button
      var discussionTabBtn = element(by.cssContainingText('.nav-tabs a', 'Discussion'));
      discussionTabBtn.click();
    });

    it('shows document comments in the "discussion" tab', function() {
      var comment1 = element(by.repeater('comment in doc.comments').row(0));
      var comment2 = element(by.repeater('comment in doc.comments').row(1));

      expect(comment1.isPresent()).toBe(true);
      expect(comment2.isPresent()).toBe(true);

      var comment1Name = comment1.element(by.css('.author'));
      var comment1Body = comment1.element(by.css('.content'));
      var comment1Time = comment1.element(by.css('.date'));

      var comment2Name = comment2.element(by.css('.author'));
      var comment2Body = comment2.element(by.css('.content'));
      var comment2Time = comment2.element(by.css('.date'));

      expect(comment1Name.getText()).toBe('First Last');
      expect(comment1Body.getText()).toBe('Yet another comment');
      expect(comment1Time.getText()).toMatch(/about \d+ (.+) ago/i);

      expect(comment2Name.getText()).toBe('First Last');
      expect(comment2Body.getText()).toBe('This is a comment');
      expect(comment2Time.getText()).toMatch(/about \d+ (.+) ago/i);
    });

    it('can show comment replies', function() {
      var parentComment = element(by.repeater('comment in doc.comments').row(1));

      // Click to reveal the comment replies
      parentComment.element(by.css('.doc-replies-count')).click();
      var reply = element(by.repeater('reply in comment.comments').row(0));
      expect(reply.isPresent()).toBe(true);

      var replyName = reply.element(by.css('.author'));
      var replyBody = reply.element(by.css('.content'));
      var replyTime = reply.element(by.css('.date'));

      expect(replyName.getText()).toBe('John Appleseed');
      expect(replyBody.getText()).toBe('Comment reply');
      expect(replyTime.getText()).toMatch(/about \d+ (.+) ago/i);
    });

  });

});
