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
    'tests/client/e2e/login.spec.js'
  ],

  baseUrl: process.env.TRAVIS_JOB_NUMBER ? 'http://localhost' : 'http://localhost:8100',

  // Options to be passed to Jasmine-node.
  // jasmineNodeOpts: {
  //   defaultTimeoutInterval: 100000
  // }

};
