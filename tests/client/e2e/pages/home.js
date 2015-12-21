var HomePage = function() {

  var docList = element.all(by.repeater('doc in docs'));
  var activeDocs = element.all(by.repeater('doc in mostActive'));
  var mostActiveDoc = activeDocs.first();
  var featuredDoc = element(by.css('.main-feature'));

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

    annotations: mostActiveDoc.element(
      by.cssContainingText('.doc-stats li', 'Annotations')
    ),
  };

  this.featuredDoc = {
    title: featuredDoc.element(by.css('.entry-title')),

    sponsor: featuredDoc.element(by.css('.author')),

    comments:featuredDoc.element(
      by.cssContainingText('.doc-stats li', 'Comments')
    ),

    annotations: featuredDoc.element(
      by.cssContainingText('.doc-stats li', 'Annotations')
    )
  };
};

module.exports = HomePage;
