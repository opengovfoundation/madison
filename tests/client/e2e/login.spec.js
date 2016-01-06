var pages = require('./pages');

// Protractor does not delete cookies/session in between tests -
// remember to log out and reset anything else you need to reuse

describe('Auth test', function() {
  var loginPage, pageLayout;

  beforeEach(function() {
    loginPage = new pages.Login();
    pageLayout = new pages.Layout();
  });

  afterEach(function() {
    loginPage.logout();
  });

  // Check simple sign in
  it('should be able to sign in', function() {
    loginPage.loginUser();

    expect(pageLayout.accountDropdown().getText())
    .toMatch(/Welcome John Appleseed/i);
  });

  // Check incorrect password sign in
  it('should fail to sign in with wrong password', function() {
    loginPage.loginWrongPass();

    expect(pageLayout.growlError().getText())
    .toMatch(/The email address or password is incorrect/i);
  });

  // Check unconfirmed email sign in
  it('should fail to sign in into unconfirmed account', function() {
    loginPage.loginUnconfirmed();

    expect(pageLayout.growlError().getText())
    .toMatch(/Please click the link sent to your email to verify your account/i);
  });

  // TODO: Forgot password tests?
  // TODO: Confirmation email tests (and resend)?

});
