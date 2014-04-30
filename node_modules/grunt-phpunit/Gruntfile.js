/*
 * grunt-phpunit
 * https://github.com/SaschaGalley/grunt-phpunit
 *
 * Copyright (c) 2013 Sascha Galley
 * http://xash.at
 * Licensed under the MIT license.
 */
'use strict';

module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    
    jshint: {
      all: [
        'Gruntfile.js',
        'tasks/**/*.js'
      ],
      options: {
        jshintrc: '.jshintrc'
      }
    },
    
    phpunit: {
      classes: {
        dir: './php/tests/'
      },
      options: {
        bin: 'vendor/bin/phpunit',
        bootstrap: 'bootstrap.php',
        configuration: 'phpunit.xml',
        colors: true
      }
    }
  });

  // Actually load this plugin's task(s).
  grunt.loadTasks('tasks');
  
  grunt.loadNpmTasks('grunt-contrib-jshint');

  // By default, lint and run all tests.
  grunt.registerTask('default', ['jshint', 'phpunit']);

};
