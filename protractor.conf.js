exports.config = {
  //sauceUser: process.env.SAUCE_USERNAME,
  //sauceKey: process.env.SAUCE_ACCESS_KEY,

  //capabilities: {
  //  'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
  //  'build': process.env.TRAVIS_BUILD_NUMBER,
  //  'name': "PHP " + process.env.TRAVIS_PHP_VERSION + "-" + process.env.TRAVIS_COMMIT_MSG
  //},

  // Spec patterns are relative to the current working directly when
  // protractor is called.
  seleniumAddress: 'http://localhost:4444/wd/hub',
  specs: ['tests/client/e2e/home.spec.js'],

  // TODO: Need a way to dynamically set this for Travis / wherever else it runs
  // * Check for TRAVIS_BASE_URL and default back to what's in .env for COOKIE_SESSION
  baseUrl: 'http://core.mymadison.local',

  // Options to be passed to Jasmine-node.
  // jasmineNodeOpts: {
  //   defaultTimeoutInterval: 100000
  // }

};
