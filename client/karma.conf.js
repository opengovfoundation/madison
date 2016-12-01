// Karma configuration
// Generated on Mon Aug 18 2014 10:30:06 GMT-0400 (EDT)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '',

    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['mocha', 'chai'],


    // list of files / patterns to load in the browser
    files: [
        'app/vendor/jquery/jquery-1.10.2.js',
        'app/vendor/select2/select2.js',
        'app/vendor/underscore.min.js',
        'app/bower_components/google-diff-match-patch-js/diff_match_patch.js',
        'app/bower_components/angular/angular.min.js',
        'app/bower_components/angular-mocks/angular-mocks.js',
        'app/bower_components/angular-animate/angular-animate.min.js',
        'app/bower_components/angular-bootstrap/ui-bootstrap.min.js',
        'app/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
        'app/bower_components/angular-cookies/angular-cookies.js',
        'app/bower_components/angular-ui/build/angular-ui.min.js',
        'app/bower_components/angular-growl/build/angular-growl.min.js',
        'app/bower_components/angular-sanitize/angular-sanitize.js',
        'app/bower_components/angular-resource/angular-resource.min.js',
        'app/bower_components/bootstrap/dist/js/bootstrap.min.js',
        'app/vendor/annotator/annotator-full.min.js',
        'app/vendor/datetimepicker/datetimepicker.js',
        'app/bower_components/moment/min/moment.min.js',
        'app/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js',
        'app/js/*.js',
        'tests/client/unit/*.spec.js'
    ],


    // list of files to exclude
    exclude: [
    ],

    // test results reporter to use
    // possible values: 'dots', 'progress'
    // available reporters: https://npmjs.org/browse/keyword/karma-reporter
    reporters: ['progress'],

    // web server port
    port: 9876,
    // enable / disable colors in the output (reporters and logs)
    colors: true,
    // level of logging
    // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
    logLevel: config.LOG_INFO,
    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,
    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],

    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: true
  });
};
