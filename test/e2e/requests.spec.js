describe('Request test', function() {

  // This is a sanity test to make sure Sauce is connecting
  // If this fails first, it's easy to troubleshoot
  it('should have a title', function() {
    browser.get('/');
    expect(browser.getTitle()).toEqual('Madison Home');
  });

  // Check simple sign in
  it('should be able to request verified status', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');

    browser.get('/user/edit/2');
    element(by.id('verify')).click();
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-danger')).getText()).toEqual('A phone number is required to request verified status.');
    browser.get('/user/edit/2');
    element(by.id('phone')).sendKeys('555-555-5555');
    element(by.id('verify')).click();
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-info')).getText()).toEqual('Your verified status has been requested.');
    browser.get('/logout');
  });
  /*
  it('should be able to request independent sponsor status', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');
    browser.get('/documents/sponsor/request');
    element(by.id('address1')).sendKeys('password');
    element(by.id('address2')).sendKeys('password');    
    element(by.id('city')).sendKeys('password');
    element(by.id('city')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-danger')).getText()).toEqual('A phone number is required to request verified status.');
    browser.get('/user/logout');
  });
*/
});