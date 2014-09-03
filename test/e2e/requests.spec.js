describe('Request test', function() {

  // Check simple sign in
  it('should fail requesting verified status without a phone number', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');

    browser.get('/user/edit/2');
    element(by.id('verify')).click();
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);
    expect(element(by.css('.alert-danger')).getText()).toEqual('A phone number is required to request verified status.');
    browser.get('/logout');
  });

  it('should be able to request verified status', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');

    browser.get('/user/edit/2');
    element(by.id('phone')).sendKeys('555-555-5555');
    element(by.id('verify')).click();
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);    
    expect(element(by.css('.alert-info')).getText()).toEqual('Your verified status has been requested.');
    browser.get('/logout');
  });
  
  it('should be able to request independent sponsor status', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);    
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');
    browser.get('/documents/sponsor/request');
    element(by.id('address1')).sendKeys('test');
    element(by.id('address2')).sendKeys('test');    
    element(by.id('city')).sendKeys('test');
    element(by.cssContainingText('option', 'Maryland')).click();
    element(by.id('postal')).sendKeys('22222');
    element(by.id('phone')).sendKeys('22222');
    element.all(by.css('.btn')).get(0).click();
    browser.sleep(4000);    
    expect(element(by.css('.alert-info')).getText()).toEqual('Your request has been received.');
    browser.get('/logout');
  });

  // verify both with admin, and check they are verified
  // create group
  // verify group
  // add members, change member roles

  // create document - here elasticsearch needs to be running
  // edit document
  // edit document metadata

});