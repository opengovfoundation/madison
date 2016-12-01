exports.config = {
  sauceUser: process.env.SAUCE_USERNAME,
  sauceKey: process.env.SAUCE_ACCESS_KEY,

  capabilities: {
    'browserName': 'chrome',
    'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
    'build': process.env.TRAVIS_BUILD_NUMBER || process.env.CIRCLE_TAG,
    'name': process.env.CIRCLECI ? process.env.CIRCLE_SHA1 : "PHP " + process.env.TRAVIS_PHP_VERSION + "-" + process.env.TRAVIS_COMMIT_MSG
  },

  // If we're in CI, use sauce, otherwise use local selenium
  seleniumAddress: (process.env.TRAVIS_JOB_NUMBER || process.env.CIRCLECI) ? null : 'http://localhost:4444/wd/hub',

  // Spec patterns are relative to the current working directly when
  // protractor is called.
  specs: [
    'tests/e2e/login.spec.js',
    'tests/e2e/layout.spec.js',
    'tests/e2e/home.spec.js',
    'tests/e2e/document.spec.js',
    'tests/e2e/logged-in/document.spec.js'
  ],

  baseUrl: 'http://0.0.0.0:8888',

  rootElement: 'html',

  // This was added to prevent sync timeouts that were happening on travis
  allScriptsTimeout: 60000,
  getPageTimeout: 60000,

  onPrepare: function() {
    browser.driver.manage().window().setSize(1024, 768);
  }

  // Options to be passed to Jasmine-node.
  // jasmineNodeOpts: {
  //   defaultTimeoutInterval: 100000
  // }

};
