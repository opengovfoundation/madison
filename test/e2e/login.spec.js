// Protractor does not delete cookies/session in between tests - 
// remember to log out and reset anything else you need to reuse

describe('Auth test', function() {

  // Check simple sign in
  it('should be able to sign in', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-info')).getText()).toEqual('You have been successfully logged in.');
    browser.get('/logout');
  });

  // Check incorrect password sign in
  it('should fail to sign in with wrong password', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test@opengovfoundation.org');
    element(by.id('password')).sendKeys('wrongpassword');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-danger')).getText()).toEqual('Incorrect login credentials');
  }); 

  // Check unconfirmed email sign in
  it('should fail to sign in into unconfirmed account', function() {
    browser.get('/user/login');
    element(by.id('email')).sendKeys('test2@opengovfoundation.org');
    element(by.id('password')).sendKeys('password');
    element.all(by.css('.btn')).get(0).click();
    expect(element(by.css('.alert-danger')).getText()).toEqual('Please click the link sent to your email to verify your account.');
  });

});