var ptor;

ptor = protractor.getInstance();

describe('madison home doc list', function () {
  it('should see a list of documents', function () {
    browser.get('http://madison');

    waitLoader = by.css('.wait-loader');

    browser.wait(function () {
      return ptor.isElementPresent(waitLoader);
    }, 8000);

    var docList = element.all(by.repeater('doc in docs'));
    docList.wait()
    console.log(docList.length);
    expect(docList.count().toBeGreaterThan(0));
  });
});