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
    clickLoginButton();
    browser.driver.wait(
      protractor.until.elementLocated(by.css('.account-dropdown')),
      5000
    );
  };

  this.loginWrongPass = function() {
    browser.get('/user/login');
    emailField.sendKeys('user@example.com');
    passwordField.sendKeys('wrongpassword');
    clickLoginButton();
  };

  this.loginUnconfirmed = function() {
    browser.get('/user/login');
    emailField.sendKeys('user2@example.com');
    passwordField.sendKeys('password');
    clickLoginButton();
  };

  this.logout = function() {
    element(by.css('.account-dropdown')).isPresent()
    .then(function(presence) {
      if (!presence) return;
      element(by.css('.account-dropdown')).click();
      element(by.css('li.link-logout a')).click();
    });
  };
};

// Convenience function to make sure login button is clickable first
// Seems to be something that is needed during Safari tests
function clickLoginButton() {
  browser.driver.wait(
    protractor.until.elementLocated(by.css('.login-button')),
    5000
  );
  loginButton.click();
}

module.exports = LoginPage;
