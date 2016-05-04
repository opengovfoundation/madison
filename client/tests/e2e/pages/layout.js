var PageLayout = function() {

  var signupBanner = element(by.cssContainingText('.prompt.prompt-info', 'Want to help'));
  var loginModal = element(by.cssContainingText('.popup', 'Please log in.'));

  var links = {
    logo: element(by.css('.logo-madison')),
    home: element(by.css('.nav-main .link-home')),
    support: element(by.cssContainingText('.nav-main .link-support', 'Support')),
    donate: element(by.cssContainingText('.nav-main .link-support', 'Donate')),
    subscribe: element(by.css('.nav-main .link-subscribe')),
    login: element(by.css('.nav-main .link-login')),
    signup: element(by.css('.nav-main .link-signup')),
    footer: {
      intro: element(by.css('.nav-footer > a'))
    }
  };

  this.links = links;

  this.signupBanner = function() {
    return signupBanner;
  };

  this.closeSignupBanner = function() {
    return signupBanner.element(by.css('.prompt-close')).click();
  };

  // This is a function because it may not be available immediately
  this.accountDropdown = function() {
    return element(by.css('.account-dropdown'));
  };

  // This is a function because it may not be available immediately
  this.growlError = function() {
    return element(by.css('.growl-item.alert-error .growl-message'));
  };

  this.loginModal = function() {
    return loginModal;
  };
};

module.exports = PageLayout;
