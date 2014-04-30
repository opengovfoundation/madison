module.exports = function (grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Task configuration
    compass: {
      dist: {
        options: {
          config: './config.rb',
          environment: 'production'
        }
      },
      dev: {
        options: {
          config: './config.rb',
        }
      }
    },
    jshint: {
      options: {
        'proto': true
      },
      all: ['public/js/*.js']
    },
    browserify: {
      js: {
        src: 'public/js/app.js',
        dest: 'public/build/app.js'
      }
    },
    uglify: {
      options: {
        mangle: false
      }
    },
    watch: {
      scripts: {
        files: ['public/js/*.js'],
        tasks: ['jshint', 'browserify']
      },
      sass: {
        files: './public/sass/**/*.scss',
        tasks: ['compass']
      }
    },
    exec: {
      install_composer: {
        cmd: 'composer install'
      },
      install_bower: {
        cmd: 'bower install'
      },
      install_npm: {
        cmd: 'npm install'
      },
      vagrant_setup: {
        cmd: 'vagrant up'
      }
    }
  });

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-browserify');
  grunt.loadNpmTasks('grunt-exec');

  // Task definition
  grunt.registerTask('default', ['jshint', 'browserify', 'watch']);
  grunt.registerTask('install', ['exec:install_composer', 'exec:install_bower', 'exec:install_npm']);
};