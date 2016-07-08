var pages = require('./pages');

describe('Document view', function() {
  var docPage, pageLayout;

  beforeEach(function() {
    docPage = new pages.Document();
    pageLayout = new pages.Layout();
    docPage.get();
  });

  it('shows the document specified by the URL', function() {
    expect(docPage.info.title.getText()).toBe('Example Document');
  });

  it('shows the document text', function() {
    expect(docPage.info.content.getText()).toMatch(/Document 1/);
  });

  describe('Document stats', function() {
    it('shows the number of participants', function() {
      expect(docPage.stats.participants.getText()).toBe('2');
    });

    it('shows the number of comments', function() {
      expect(docPage.stats.comments.getText()).toBe('3');
    });

    it('shows the number of annotations', function() {
      expect(docPage.stats.annotations.getText()).toBe('2');
    });

    it('shows how recently the document was updated', function() {
      expect(docPage.stats.updated.getText())
      .toMatch(/updated (about )?(\d+|a) .+ ago/i);
    });

    it('shows support and opposition', function() {
      expect(docPage.stats.supportCount.getText()).toBe('0');
      expect(docPage.stats.supportChart.getAttribute('width')).toBe('0');
      expect(docPage.stats.opposeCount.getText()).toBe('0');
      expect(docPage.stats.supportChart.getAttribute('width')).toBe('0');
    });
  });

  describe('Document comments', function() {
    beforeEach(function() {
      docPage.showComments();
    });

    it('shows document comments in the "comments" tab', function() {
      var comment1 = docPage.getComment(0);
      var comment2 = docPage.getComment(1);

      expect(comment1.name.getText()).toBe('First Last');
      expect(comment1.body.getText()).toBe('Yet another comment');
      expect(comment1.time.getText()).toMatch(/(about )?(\d+|a) (.+) ago/i);

      expect(comment2.name.getText()).toBe('First Last');
      expect(comment2.body.getText()).toBe('This is a comment');
      expect(comment2.time.getText()).toMatch(/(about )?(\d+|a) (.+) ago/i);
    });

    it('can show comment replies', function() {
      var parentComment = docPage.getComment(1);
      docPage.showCommentRepliesByComment(parentComment);
      var reply = docPage.getCommentReply(0);

      expect(reply.name.getText()).toBe('John Appleseed');
      expect(reply.body.getText()).toBe('Comment reply');
      expect(reply.time.getText()).toMatch(/(about )?(\d+|a) (.+) ago/i);
    });

    it('shows login modal when clicking "login to comment"', function() {
      docPage.loginToCommentLink().click();
      expect(pageLayout.loginModal().isDisplayed()).toBe(true);
    });

    describe('clicking document text tab', function() {
      it('hides comments tab, shows document text', function() {
        docPage.showDocumentText();
        expect(docPage.info.content.getText()).toMatch(/Document 1/);
        expect(docPage.info.content.isDisplayed()).toBe(true);
      });
    });

  });

  describe('Support / Oppose', function() {
    it('shows login or signup prompt when trying to support', function() {
      docPage.buttons.support.click();
      expect(pageLayout.loginModal().isDisplayed()).toBe(true);
    });

    it('shows login or signup prompt when trying to oppose', function() {
      docPage.buttons.oppose.click();
      expect(pageLayout.loginModal().isDisplayed()).toBe(true);
    });
  });

  describe('Table of Contents', function() {
    it('shows table of contents when clicking the toggle', function() {
      docPage.showTableOfContents();
      expect(docPage.pageWithTableOfContents().isPresent()).toBe(true);
    });

    it('closes table of contents when clicking hide', function() {
      docPage.showTableOfContents();
      docPage.hideTableOfContents();
      expect(docPage.pageWithTableOfContents().isPresent()).toBe(false);
    });
  });

});
