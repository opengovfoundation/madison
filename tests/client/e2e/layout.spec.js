var pages = require('./pages');

describe('Madison page layout', function() {
  var pageLayout;

  beforeEach(function() {
    pageLayout = new pages.Layout();
    browser.get('/');
  });

  describe('signup banner', function() {
    it('should display the signup banner if not logged in', function() {
      expect(pageLayout.signupBanner().isPresent()).toBe(true);
    });

    it('should hide the banner if the X is clicked', function() {
      pageLayout.closeSignupBanner();
      expect(pageLayout.signupBanner().isPresent()).toBe(false);
    });
  });

  describe('navigation links', function() {
    it('takes us to the home page when clicking banner logo', function() {
      browser.get('/about'); // Go somewhere else first
      pageLayout.links.logo.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/');
    });

    it('takes us home when clicking the HOME nav link', function() {
      browser.get('/about'); // Go somewhere else first
      pageLayout.links.home.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/');
    });

    it('takes us to the about page when clicking the ABOUT nav link',
    function() {
      pageLayout.links.about.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/about');
    });

    it('takes us to the faq page when clicking the FAQ nav link',
    function() {
      pageLayout.links.faq.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/faq');
    });

    // Just check href on DONATE and SUBSCRIBE links, since they're external

    it('takes us to the donate page when clicking the DONATE nav link',
    function() {
      expect(
        pageLayout.links.donate.element(by.css('a')).getAttribute('href')
      ).toMatch(/paypal/);
      expect(
        pageLayout.links.donate.element(by.css('a')).getAttribute('target')
      ).toBe('_blank');
    });

    it('takes us to the subscribe page when clicking the SUBSCRIBE nav link',
    function() {
      expect(
        pageLayout.links.subscribe.element(by.css('a')).getAttribute('href')
      ).toMatch(/list-manage/);
      expect(
        pageLayout.links.subscribe.element(by.css('a')).getAttribute('target')
      ).toBe('_blank');
    });

    it('takes us to the login page when clicking the LOGIN nav link',
    function() {
      pageLayout.links.login.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/user/login');
    });

    it('takes us to the signup page when clicking the SIGNUP nav link',
    function() {
      pageLayout.links.signup.click();
      expect(browser.getCurrentUrl()).toBe('http://0.0.0.0:8100/user/signup');
    });
  });

  describe('footer links', function() {});
});
