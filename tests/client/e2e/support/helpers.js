module.exports = {

  loginUser: function() {
    browser.get('/user/login');
    element(by.name('email')).sendKeys('user@example.com');
    element(by.name('password')).sendKeys('password');
    element(by.css('.login-button')).click();
    expect(element(by.css('.account-dropdown')).getText()).toMatch(/Welcome John Appleseed/i);
  },

  loginAdmin: function() {
    browser.get('/user/login');
    element(by.name('email')).sendKeys('admin@example.com');
    element(by.name('password')).sendKeys('password');
    element(by.css('.login-button')).click();
    expect(element(by.css('.account-dropdown')).getText()).toMatch(/Welcome First Last/i);
  }

};
