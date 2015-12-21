var DocumentPage = function() {
  var docInfo = element(by.css('.doc-info'));


  this.get = function() {
    browser.get('/docs/example-document');
  };

  this.docInfo = {
    title: docInfo.element(by.css('.heading'))
  };

  this.stats = {
    supportCount: element(by.binding('doc.support')),
    supportChart: element(by.css('.support-chart .chart .support rect')),
    opposeCount: element(by.binding('doc.oppose')),
    opposeChart: element(by.css('.support-chart .chart .oppose rect'))
  };

  this.buttons = {
    support: element(by.css('#doc-support')),
    oppose: element(by.css('#doc-oppose'))
  };

};

module.exports = DocumentPage;
