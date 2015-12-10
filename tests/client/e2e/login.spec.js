// Protractor does not delete cookies/session in between tests -
// remember to log out and reset anything else you need to reuse

describe('Auth test', function() {

  afterEach(function() {
    browser.get('/logout');
  });

  // Check simple sign in
  it('should be able to sign in', function() {
    browser.get('/user/login');
    element(by.name('email')).sendKeys('user@example.com');
    element(by.name('password')).sendKeys('password');
    element(by.css('.login-button')).click();
    expect(element(by.css('.account-dropdown')).getText()).toMatch(/Welcome John Appleseed/i);
  });

  // Check incorrect password sign in
  it('should fail to sign in with wrong password', function() {
    browser.get('/user/login');
    element(by.name('email')).sendKeys('user@example.com');
    element(by.name('password')).sendKeys('wrongpassword');
    element(by.css('.login-button')).click();
    expect(element(by.css('.growl-item.alert-error .growl-message')).getText()).toMatch(/The email address or password is incorrect/i);
  });

  // Check unconfirmed email sign in
  it('should fail to sign in into unconfirmed account', function() {
    browser.get('/user/login');
    element(by.name('email')).sendKeys('user2@example.com');
    element(by.name('password')).sendKeys('password');
    element(by.css('.login-button')).click();
    expect(element(by.css('.growl-item.alert-error .growl-message')).getText()).toMatch(/Please click the link sent to your email to verify your account/i);
  });

});
