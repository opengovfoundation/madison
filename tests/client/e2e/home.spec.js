var pages = require('./pages');

describe('madison home doc list', function() {
  var homePage, docPage;

  beforeEach(function() {
    homePage = new pages.Home();
    docPage = new pages.Document();
    homePage.get();
  });

  it('should see a list of documents', function() {
    expect(homePage.docListCount()).toBe(2);
  });

  it('should display a list of most active documents', function() {
    expect(homePage.mostActiveDoc.title.getText()).toBe('"Example Document"');
    expect(homePage.mostActiveDoc.comments.getText()).toBe('3 Comments');
    expect(homePage.mostActiveDoc.annotations.getText()).toBe('2 Annotations');
  });

  it('should have a featured document', function() {
    expect(homePage.featuredDoc.title.getText()).toBe('Example Document');
    expect(homePage.featuredDoc.sponsor.getText()).toBe('Example Group Display');
    expect(homePage.featuredDoc.comments.getText()).toBe('3 Comments');
    expect(homePage.featuredDoc.annotations.getText()).toBe('2 Annotations');
  });

  it('goes to the document page when featured title is clicked', function() {
    homePage.featuredDoc.title.click();
    expect(docPage.info.title.getText()).toBe('Example Document');
  });

  it('has a usable "read" button in recent legislation list', function() {
    homePage.mostRecentDoc().readButton.click();
    expect(docPage.info.title.getText()).toBe('Second Example Document');
  });

  describe('searching recent documents', function() {
    it('filters documents in "recent" list based on search term', function() {
      homePage.searchDocs('second example');
      expect(homePage.recentList.count()).toBe(1);
    });
  });

  // TODO: Test that "Recent Activity", "Recent Legislation and "Most Active
  // Documents" show in correct orders. Will require some kind of control over
  // document and activity dates. Currently the database seeding just takes
  // current timestamps, so that will need to be adjusted.

});
