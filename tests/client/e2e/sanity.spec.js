describe('Sanity test', function() {

  // This is a sanity test to make sure Sauce is connecting
  // If this fails first, it's easy to troubleshoot
  it('should be able to see the page title', function() {
    browser.get('/');
    expect(browser.getTitle()).toEqual('Madison Home');
  });

});