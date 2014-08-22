describe('Auth tests', function() {

  // This is a sanity test to make sure Sauce is connecting
  // If this fails first, it's easy to troubleshoot
  it('should have a title', function() {
    browser.get('/');
    expect(browser.getTitle()).toEqual('Madison Home');
  });

  // Check simple sign in
  it('should be able to sign in', function() {

    browser.get('/user/login');
    element(by.id('email')).sendKeys('admin@example.com');
    element(by.id('password')).sendKeys('password');

    // Click the first button on the page - this may change
    element.all(by.css('.btn')).get(0).click();

    // Multiple alerts will make this unreliable
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');
  });


});