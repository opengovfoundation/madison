/*
 * grunt-selenium-webdriver
 * https://github.com/connectid/grunt-selenium-webdriver
 * gives you three tasks selenium_start, selenium_phantom_hub (single client instance for headless testing), selenium_stop
 * eg  grunt.registerTask('startstop', ['selenium_start', 'selenium_stop','selenium_phantom_hub', 'selenium_stop']);
 * note server will be stopped automatically when grunt exits even without explicit selenium_stop
 *
 * Copyright (c) 2014 ConnectiD
 * Licensed under the MIT license.
 */

'use strict';

module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    jshint: {
      all: [
        'Gruntfile.js',
        'tasks/*.js',
        '<%= nodeunit.tests %>'
      ],
         options: {
        jshintrc: '.jshintrc'
      }
    },

    // Before generating any new files, remove any previously-created files.
    clean: {
      tests: ['tmp']
    },

    mochacli: {
      options: {
          colors:        true,
          'check-leaks': false,
          ui:            'bdd',
          reporter:      'spec',
          timeout:       20000
      },
      e2e: {
          options: {
              files: ['test/*.js']
          }
      }
    },

    selenium_start: {
        options: { port: 4445 }
    },
    selenium_phantom_hub: {
        options: { port: 4445 }
    },
    selenium_stop: {
        options: { }
    },

    connect: {
      e2e: {
          options: {
              port: 9000,
              base: 'test/fixtures',
              hostname: '*'
          }
      }
    }

  });

  // Actually load this plugin's task(s).
  grunt.loadTasks('tasks');

  // These plugins provide necessary tasks.
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-connect');
  grunt.loadNpmTasks( 'grunt-mocha-cli' ); //testing alternative runner


  // Whenever the "test" task is run, first clean the "tmp" dir, then run this
  // plugin's task(s), then test the result.
  grunt.registerTask('test', ['clean',
        'selenium_start' ,
        'selenium_stop' ,
        'selenium_phantom_hub',
        'connect:e2e',
        'mochacli:e2e',
        'selenium_stop']);

  // By default, lint and run all tests.
  grunt.registerTask('default', ['jshint', 'test']);

};
