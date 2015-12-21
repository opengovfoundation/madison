exports.config = {
  sauceUser: process.env.SAUCE_USERNAME,
  sauceKey: process.env.SAUCE_ACCESS_KEY,

  capabilities: {
    'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
    'build': process.env.TRAVIS_BUILD_NUMBER,
    'name': "PHP " + process.env.TRAVIS_PHP_VERSION + "-" + process.env.TRAVIS_COMMIT_MSG
  },

  // If we're in travis, use sauce, otherwise use local selenium
  seleniumAddress: process.env.TRAVIS_JOB_NUMBER ? null : 'http://localhost:4444/wd/hub',

  // Spec patterns are relative to the current working directly when
  // protractor is called.
  specs: [
    'tests/client/e2e/home.spec.js',
    'tests/client/e2e/login.spec.js',
    'tests/client/e2e/document.spec.js',
    'tests/client/e2e/logged-in/document.spec.js'
  ],

  baseUrl: 'http://0.0.0.0:8100',

  onPrepare: function() {
    browser.driver.manage().window().setSize(1024, 768);
  }

  // Options to be passed to Jasmine-node.
  // jasmineNodeOpts: {
  //   defaultTimeoutInterval: 100000
  // }

};
