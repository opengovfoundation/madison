exports.config = {
  sauceUser: process.env.SAUCE_USERNAME,
  sauceKey: process.env.SAUCE_ACCESS_KEY,

  capabilities: {
    'tunnel-identifier': process.env.TRAVIS_JOB_NUMBER,
    'build': process.env.TRAVIS_BUILD_NUMBER,
    'name': "PHP " + process.env.TRAVIS_PHP_VERSION + "-" + process.env.TRAVIS_COMMIT_MSG 
  },

  // Spec patterns are relative to the current working directly when
  // protractor is called.
  specs: ['test/e2e/*.spec.js'],
  baseUrl: 'http://127.0.0.1:9000',
  // Options to be passed to Jasmine-node.
  jasmineNodeOpts: {
    defaultTimeoutInterval: 100000 
  }

};
