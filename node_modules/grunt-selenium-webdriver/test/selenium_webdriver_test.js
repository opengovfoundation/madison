'use strict';

var grunt = require('grunt'),
    webdriver = require('selenium-webdriver');

/*
  ======== A Handy Little Nodeunit Reference ========
  https://github.com/caolan/nodeunit

  Test methods:
    test.expect(numAssertions)
    test.done()
  Test assertions:
    test.ok(value, [message])
    test.equal(actual, expected, [message])
    test.notEqual(actual, expected, [message])
    test.deepEqual(actual, expected, [message])
    test.notDeepEqual(actual, expected, [message])
    test.strictEqual(actual, expected, [message])
    test.notStrictEqual(actual, expected, [message])
    test.throws(block, [error], [message])
    test.doesNotThrow(block, [error], [message])
    test.ifError(value)
*/

var client;
exports.selenium_webdriver = {
  setUp: function(done) {
    // just testing phantomjs for now as would need to check environment otherwise, basic start / stop have to work to get here anyway
    var serverConfig = 'http://127.0.0.1:4445/wd/hub',
    capabilities = {
        silent: false,
        browserName: 'phantomjs' };


    client = new webdriver.Builder().
        usingServer( serverConfig ).
        withCapabilities( capabilities).
        build();
    done();
  },
  get_eckhart: function(test) {
    test.expect(1);
    client.get ( 'http://google.com/' );
    client.getTitle().then( function ( title ) {
        console.log ( title );
        test.ok ( title === 'Google' , 'Fetched Google');
        test.done();
    });
  }
};

