var HomePage = function() {

  var docList = element.all(by.repeater('doc in docs'));
  var activeDocs = element.all(by.repeater('doc in mostActive'));
  var mostActiveDoc = activeDocs.first();
  var featuredDoc = element(by.css('.main-feature'));
  var recentList = this.recentList = element.all(by.css('.search-list .doc-list-item'));
  var searchBox = element(by.css('input#doc-text-filter'));
  var currentCategory = this.currentCategory = element(by.css('.category-filter .category'));
  var categoryFilter = this.categoryFilter = element(by.css('.category-filter'));
  var clearCategoryBtn = element(by.css('a.clear-category'));

  this.get = function() {
    browser.get('/');
  };

  this.docListCount = function() {
    return docList.count();
  };

  this.activeDocCount = function() {
    return activeDocs.count();
  };

  this.mostActiveDoc = {
    title: mostActiveDoc.element(by.css('.title')),

    comments: mostActiveDoc.element(
      by.cssContainingText('.doc-stats li', 'Comments')
    ),

    notes: mostActiveDoc.element(
      by.cssContainingText('.doc-stats li', 'Notes')
    ),
  };

  this.featuredDoc = {
    title: featuredDoc.element(by.css('.entry-title')),

    sponsor: featuredDoc.element(by.css('.author')),

    comments:featuredDoc.element(
      by.cssContainingText('.doc-stats li', 'Comments')
    ),

    notes: featuredDoc.element(
      by.cssContainingText('.doc-stats li', 'Notes')
    )
  };

  this.mostRecentDoc = function() {
    var mostRecent = recentList.first();
    return {
      title: mostRecent.element(by.css('.doc-info h3')),
      readButton: mostRecent.element(by.css('.read-action a.action-button'))
    };
  };

  this.leastRecentDoc = function() {
    var leastRecent = recentList.first();
    return {
      title: leastRecent.element(by.css('.doc-info h3')),

      readButton: leastRecent.element(by.css('.read-action a.action-button')),

      findCategory: function(idx) {
        return leastRecent.element(
          by.css('.doc-categories li:nth-child(' + idx + ')')
        );
      }
    };
  };

  this.searchDocs = function(term) {
    searchBox.sendKeys(term);
    searchBox.sendKeys(protractor.Key.ENTER);
  };

  this.clearCategory = function() {
    clearCategoryBtn.click();
  };
};

module.exports = HomePage;
