//var ptor = protractor.getInstance();
var ptor = require('protractor');
var fs = require('fs');

describe('madison home doc list', function () {
  beforeEach(function() {
    browser.get('/');
  });

  it('should see a list of documents', function () {
    var docList = element.all(by.repeater('doc in docs'));
    expect(docList.count()).toBe(2);
  });
});
