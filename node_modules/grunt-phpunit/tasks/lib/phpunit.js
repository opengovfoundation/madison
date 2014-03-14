/*
 * grunt-phpunit
 * https://github.com/SaschaGalley/grunt-phpunit
 *
 * Copyright (c) 2013 Sascha Galley
 * http://xash.at
 * Licensed under the MIT license.
 */
'use strict';

// External libs.
var exec = require('child_process').exec;

exports.init = function(grunt) {


  /**
   * Runs phpunit command with options
   *
   * @param String command
   * @param Function callback
   * @param Object config
   */
  exports.run = function(command, callback, config) {

    var term = exec(command, function(err, stdout, stderr) {

      if (stdout && !config.followOutput) {
        grunt.log.write(stdout);
      }

      if (err) {
        grunt.fatal(err);
      }
      callback();
    });

    if (config.followOutput) {
      term.stdout.on('data', function(data) {
        grunt.log.write(data);
      });

      term.stderr.on('data', function(data) {
        grunt.log.error(data);
      });
    }
  };

  return exports;
};