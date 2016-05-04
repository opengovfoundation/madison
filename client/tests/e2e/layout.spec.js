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
      browser.get('/user/login'); // Go somewhere else first
      pageLayout.links.logo.click();
      expect(browser.getCurrentUrl()).toBe('http://test.mymadison.local/');
    });

    it('takes us home when clicking the HOME nav link', function() {
      browser.get('/user/login'); // Go somewhere else first
      pageLayout.links.home.click();
      expect(browser.getCurrentUrl()).toBe('http://test.mymadison.local/');
    });

    it('takes us to the login page when clicking the LOGIN nav link',
    function() {
      pageLayout.links.login.click();
      expect(browser.getCurrentUrl()).toBe('http://test.mymadison.local/user/login');
    });

    it('takes us to the signup page when clicking the SIGNUP nav link',
    function() {
      pageLayout.links.signup.click();
      expect(browser.getCurrentUrl()).toBe('http://test.mymadison.local/user/signup');
    });
  });

  describe('footer links', function() {
    it('takes you to about page for each "for ___" link', function() {
      pageLayout.links.footer.intro.click();
      expect(browser.getCurrentUrl()).toBe('http://test.mymadison.local/about');
    });
  });
});
