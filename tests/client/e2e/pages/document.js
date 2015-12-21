var DocumentPage = function() {
  var docInfo = element(by.css('.doc-info'));

  var supportBtn = element(by.css('.doc-support-button'));
  var supportCount = element(by.binding('doc.support'));
  var supportChart = element(by.css('.support-chart .chart .support rect'));

  var opposeBtn = element(by.css('.doc-oppose-button'));
  var opposeCount = element(by.binding('doc.oppose'));
  var opposeChart = element(by.css('.support-chart .chart .oppose rect'));

  var supportOpposeButtons = element(by.css('.support-oppose-buttons'));

  this.get = function() {
    browser.get('/docs/example-document');
  };

  this.docInfo = {
    title: docInfo.element(by.css('.heading'))
  };

};

module.exports = DocumentPage;
