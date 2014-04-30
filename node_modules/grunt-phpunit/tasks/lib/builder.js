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
var path = require('path');

exports.init = function(grunt) {

  var exports   = {},
      _         = grunt.util._,
      directory = null,
      config    = {};

  /**
   * @var object default values
   */
  var defaults = {
    bin: 'phpunit',
    bootstrap: false,
    colors: false,
    coverage: false,
    debug: false,
    verbose: false,
    configuration: false,
    logJunit: false,
    logTap: false,
    logJson: false,
    coverageHtml: false,
    coverageClover: false,
    coveragePhp: false,
    coverageText: false,
    testdoxHtml: false,
    testdoxText: false,
    filter: false,
    group: false,
    excludeGroup: false,
    listGroups: false,
    loader: false,
    printer: false,
    repeat: false,
    tap: false,
    testdox: false,
    stderr: false,
    stopOnError: false,
    stopOnFailure: false,
    stopOnSkipped: false,
    stopOnIncomplete: false,
    strict: false,
    processIsolation: false,
    noGlobalsBackup: false,
    staticBackup: false,
    noConfiguration: false,
    includePath: false,
    d: false,
    followOutput: false
  };

  /**
   * @var Object containing flag options
   */
  var flags = {
    colors: 'colors',
    coverage: 'coverage-text',
    coverageText: 'coverage-text',
    debug: 'debug',
    verbose: 'verbose',
    tap: 'tap',
    testdox: 'testdox',
    stderr: 'stderr',
    strict: 'strict',
    listGroups: 'list-groups',
    stopOnError: 'stop-on-error',
    stopOnFailure: 'stop-on-failure',
    stopOnSkipped: 'stop-on-skipped',
    stopOnIncomplete: 'stop-on-incomplete',
    processIsolation: 'process-isolation',
    noGlobalsBackup: 'no-globals-backup',
    staticBackup: 'static-backup',
    noConfiguration: 'no-configuration'
  };

  /**
   * @var Array contains file options
   */
  var files = [
    'bootstrap',
    'configuration'
  ];

  /**
   * @var Object containing valued options
   */
  var valued = {
    logJunit: 'log-junit',
    logTap: 'log-tap',
    logJson: 'log-json',
    coverageText: 'coverage-text',
    coverageHtml: 'coverage-html',
    coverageClover: 'coverage-clover',
    coveragePhp: 'coverage-php',
    testdoxHtml: 'testdox-html',
    testdoxText: 'testdox-text',
    filter: 'filter',
    group: 'group',
    excludeGroup: 'exclude-group',
    loader: 'loader',
    printer: 'printer',
    repeat: 'repeat',
    includePath: 'include-path',
    d: 'd'
  };

  /**
   * Builds flag options
   *
   * @return array
   */
  var buildFlagOptions = function() {

    var options = [];

    _.each(flags, function(value, key) {
      if(grunt.option(key) || grunt.option(value) ||  config[key] === true) {
        options.push('--' + value);
      }
    });
    return options;
  };

  /**
   * Builds file options
   *
   * @return array
   */
  var buildFileOptions = function() {

    var options = [];

    _.each(files, function(file) {

      if(!config[file]) {
        return;
      }
      if (grunt.file.exists(directory + config[file])) {
        options.push('--'+ file + ' ' + directory + config[file]);
      } else {
        options.push('--'+ file + ' ' + config[file]);
      }
    });
    return options;
  };

  /**
   * Builds valued options
   *
   * @return array
   */
  var buildValuedOptions = function() {

    var options = [];

    _.each(valued, function(value, key) {
      if (grunt.option(value)) {
        options.push('--'+value+' '+grunt.option(value));
      } else if(config[key]) {
        options.push('--'+value+'="'+config[key]+'"');
      }
    });
    return options;
  };

  /**
   * Builds phpunit command
   *
   * @return string
   */
  var buildOptions = function() {

    var options = [].concat(
      buildFlagOptions(),
      buildFileOptions(),
      buildValuedOptions()
    );
    return options.join(' ');
  };

  /**
   * Returns the command to be run
   *
   */
  var command = function() {
    return path.normalize(config.bin);
  };

  /**
   * Returns the directory that phpunit will be run from
   *
   * @return string
   */
  exports.directory = function() {
    return directory;
  };

  /**
   * Setup task before running it
   *
   * @param Object runner
   */
  exports.build = function(dir, options) {
    directory = dir ? path.normalize(dir) : '';
    config    = options(defaults);

    return command() + ' ' + buildOptions() + ' ' + directory;
  };

  /**
   * Returns the phpunit config object
   *
   * @return string
   */
  exports.config = function() {
    return config;
  };

  return exports;
};
