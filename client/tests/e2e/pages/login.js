var LoginPage = function() {
  var loginButton = element(by.css('.login-button'));
  var emailField = element(by.name('email'));
  var passwordField = element(by.name('password'));

  this.get = function() {
    browser.get('/user/login');
  };

  this.loginUser = function() {
    browser.get('/user/login');
    emailField.sendKeys('user@example.com');
    passwordField.sendKeys('password');
    loginButton.click();
    browser.driver.wait(
      protractor.until.elementLocated(by.css('.account-dropdown > .dropdown-trigger')),
      5000
    );
  };

  this.loginWrongPass = function() {
    browser.get('/user/login');
    emailField.sendKeys('user@example.com');
    passwordField.sendKeys('wrongpassword');
    loginButton.click();
  };

  this.loginUnconfirmed = function() {
    browser.get('/user/login');
    emailField.sendKeys('user2@example.com');
    passwordField.sendKeys('password');
    loginButton.click();
  };

  this.loginInvalid = function() {
    browser.get('/user/login');
    emailField.sendKeys('doesnotexist@example.com');
    passwordField.sendKeys('password');
    loginButton.click();
  };

  this.logout = function() {
    element(by.css('.account-dropdown > .dropdown-trigger')).isPresent()
    .then(function(presence) {
      if (!presence) return;
      //browser.sleep(100);
      element(by.css('.account-dropdown > .dropdown-trigger')).click();
      element(by.css('li.link-logout a')).click();
    });
  };
};

module.exports = LoginPage;
