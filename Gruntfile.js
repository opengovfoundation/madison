module.exports = function (grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    // Task configuration
    compass: {
      dist: {
        options: {
          config: './public/config.rb',
          environment: 'production'
        }
      },
      dev: {
        options: {
          config: './public/config.rb',
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
      },
      components: {
        files: {
          './public/components.js': './public/components.js'
        }
      },
      main: {
        files: {
          './public/main.js': './public/main.js'
        }
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
    }
  });

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-browserify');

  // Task definition
  grunt.registerTask('default', ['jshint', 'browserify', 'watch']);
};