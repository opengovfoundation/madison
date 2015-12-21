var PageLayout = function() {

  this.links = {
    home: element(by.css('.nav-main .link-home')),
    about: element(by.css('.nav-main .link-about')),
    faq: element(by.css('.nav-main .link-faq')),
    support: element(by.cssContainingText('.nav-main .link-support', 'Support')),
    donate: element(by.cssContainingText('.nav-main .link-support', 'Donate')),
    subscribe: element(by.css('.nav-main .link-subscribe')),
    login: element(by.css('.nav-main .link-login')),
    signup: element(by.css('.nav-main .link-signup'))
  };

  // This is a function because it may not be available immediately
  this.accountDropdown = function() {
    return element(by.css('.account-dropdown'));
  };

  // This is a function because it may not be available immediately
  this.growlError = function() {
    return element(by.css('.growl-item.alert-error .growl-message'));
  };
};

module.exports = PageLayout;
