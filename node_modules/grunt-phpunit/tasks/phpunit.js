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

  // Internal lib.
  var builder = require('./lib/builder').init(grunt);
  var phpunit = require('./lib/phpunit').init(grunt);

  grunt.registerMultiTask( 'phpunit', 'Run phpunit', function() {

    var directory = this.data.dir || '';

    delete this.data.dir;
    var options = this.options(this.data);

    var command = builder.build(directory, function(config) {
      // Merge task options with global options
      Object.keys(options).forEach(function(key) {
        config[key] = options[key];
      });

      return config;
    });

    // Run the command
    grunt.log.writeln('Starting phpunit (target: ' + this.target.cyan + ') in ' + builder.directory().cyan);
    grunt.verbose.writeln('Exec: ' + command);

    phpunit.run(command, this.async(), builder.config());
  });

};
