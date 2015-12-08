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
  specs: [
    'tests/client/e2e/home.spec.js',
    'tests/client/e2e/login.spec.js'
  ],

  // TODO: pull in dotenv for node and grab the `COOKIE_SESSION` env var
  baseUrl: 'http://core.mymadison.local',

  // Options to be passed to Jasmine-node.
  // jasmineNodeOpts: {
  //   defaultTimeoutInterval: 100000
  // }

};
