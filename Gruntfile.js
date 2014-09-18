module.exports = function (grunt) {

  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    notify: {
      uglify: {
        options: {
          message: 'Uglify complete.'
        }
      },
      cssmin: {
        options: {
          message: "Cssmin complete."
        }
      }
    },
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
      all: [
        'public/js/bootstrap-tour.js',
        'public/js/controllers.js',
        'public/js/dashboardControllers.js',
        'public/js/services.js',
        'public/js/directives.js',
        'public/js/filters.js',
        'public/js/annotationServiceGlobal.js',
        'public/js/app.js'
      ]
    },
    uglify: {
      frontend_target: {
        files: {
          'public/build/app.js': [
            'public/vendor/jquery/jquery-1.10.2.js',
            'public/vendor/select2/select2.js',
            'public/vendor/underscore.min.js',
            'public/bower_components/google-diff-match-patch-js/diff_match_patch.js',
            'public/bower_components/angular/angular.min.js',
            'public/bower_components/angular-animate/angular-animate.min.js',
            'public/bower_components/angular-bootstrap/ui-bootstrap.min.js',
            'public/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
            'public/bower_components/angular-cookies/angular-cookies.js',
            'public/bower_components/angular-ui/build/angular-ui.min.js',
            'public/bower_components/zeroclipboard/dist/ZeroClipboard.min.js',
            'public/bower_components/angular-growl/build/angular-growl.min.js',
            'public/bower_components/angular-sanitize/angular-sanitize.js',
            'public/bower_components/angular-resource/angular-resource.min.js',
            'public/bower_components/angular-route/angular-route.min.js',
            'public/bower_components/bootstrap/dist/js/bootstrap.min.js',
            'public/bower_components/pagedown/Markdown.Converter.js',
            'public/bower_components/pagedown/Markdown.Sanitizer.js',
            'public/bower_components/pagedown/Markdown.Editor.js',
            'public/bower_components/crypto-js/index.js',
            'public/bower_components/google-translate/index.txt',
            'public/bower_components/bootstrap/js/collapse.js',
            'public/bower_components/bootstrap/js/modal.js',
            'public/bower_components/angular-tour/dist/angular-tour.min.js',
            'public/bower_components/angular-tour/dist/angular-tour-tpls.min.js',

            //Datetimepicker and dependencies
            'public/vendor/datetimepicker/datetimepicker.js',
            'public/bower_components/moment/min/moment.min.js',
            'public/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js',

            //Annotator JS
            'public/vendor/annotator/annotator-full.min.js',
            'public/vendor/showdown/showdown.js',
            'public/js/annotator-madison.js',

            //Custom JS
            'public/js/bootstrap-tour.js',
            'public/js/controllers.js',
            'public/js/resources.js',
            'public/js/dashboardControllers.js',
            'public/js/services.js',
            'public/js/directives.js',
            'public/js/filters.js',
            'public/js/annotationServiceGlobal.js',
            'public/js/app.js',
            'public/js/googletranslate.js'
          ]
        }
      },
      options: {
        mangle: false,
        sourceMap: 'public/build/app.map'
      }
    },
    cssmin: {
      combine: {
        files: {
          'public/build/app.css': [
            'public/bower_components/angular-tour/dist/angular-tour.css',
            'public/bower_components/angular-growl/build/angular-growl.min.css',
            'public/vendor/pagedown/assets/demo.css',
            'public/vendor/datetimepicker/datetimepicker.css',
            'public/vendor/jquery/jquery-ui-smoothness.css',
            'public/vendor/bootstrap/css/bootstrap.min.css',
            'public/vendor/bootstrap/css/bootstrap-theme.min.css',
            'public/vendor/select2/select2.css',
            'public/vendor/annotator/annotator.min.css',
            'public/css/style.css',
            'public/css/dropdown-sub.css'
          ]
        }
      }
    },
    watch: {
      scripts: {
        files: ['public/js/*.js', 'Gruntfile.js'],
        tasks: ['jshint', 'uglify', 'notify:uglify']
      },
      sass: {
        files: './public/sass/*.scss',
        tasks: ['compass', 'cssmin', 'notify:cssmin']
      }
    },
    exec: {
      install_composer: {
        cmd: 'composer self-update && composer install --prefer-dist --no-interaction'
      },
      install_bower: {
        cmd: 'bower install'
      },
      install_npm: {
        cmd: 'npm install'
      },
      vagrant_setup: {
        cmd: 'vagrant up'
      },
      create_testdb: {
        cmd: function () {
          var database = "madison_grunt_test";
          var user = "root";
          var command = 'mysqladmin -u' + user + " create " + database;
          return command;
        }
      },
      drop_testdb: {
        cmd: function () {
          var database = "madison_grunt_test";
          var user = "root";
          return 'mysql -u' + user + " -e 'DROP DATABASE IF EXISTS " + database + ";'";
        }
      },
      migrate: {
        cmd: "php artisan migrate"
      },
      seed: {
        cmd: "php artisan db:seed"
      }
    },
    karma: {
      unit: {
        configFile: 'karma.conf.js'
      }
    },
    protractor: {
      options: {
        configFile: "protractor.conf.js", // Default config file
        keepAlive: false, // If false, the grunt process stops when the test fails.
        noColor: false // If true, protractor will not use colors in its output.
      },
      chrome: {
        options: {
          args: {
            sauceUser: process.env.SAUCE_USERNAME,
            sauceKey: process.env.SAUCE_ACCESS_KEY,
            browser: "chrome"
          }
        }
      },
      firefox: {
        options: {
          args: {
            sauceUser: process.env.SAUCE_USERNAME,
            sauceKey: process.env.SAUCE_ACCESS_KEY,
            browser: "firefox"
          }
        }
      },
      ie: {
        options: {
          args: {
            sauceUser: process.env.SAUCE_USERNAME,
            sauceKey: process.env.SAUCE_ACCESS_KEY,
            browser: "internet explorer"
          }
        }
      },
      safari: {
        options: {
          args: {
            sauceUser: process.env.SAUCE_USERNAME,
            sauceKey: process.env.SAUCE_ACCESS_KEY,
            browser: "safari"
          }
        }
      }
    },
  });

  // Plugin loading
  grunt.loadNpmTasks('grunt-contrib-compass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-exec');
  grunt.loadNpmTasks('grunt-mysql-dump');
  grunt.loadNpmTasks('grunt-karma');
  grunt.loadNpmTasks('grunt-protractor-runner');
  grunt.loadNpmTasks('grunt-contrib-connect');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-notify');

  // Task definition
  grunt.registerTask('build', ['jshint', 'uglify', 'notify:uglify', 'compass', 'cssmin', 'notify:cssmin']);
  grunt.registerTask('default', ['jshint', 'uglify', 'notify:uglify', 'compass', 'cssmin', 'notify:cssmin', 'watch']);
  grunt.registerTask('install', ['exec:install_composer']);
  grunt.registerTask('test_setup', ['exec:drop_testdb', 'exec:create_testdb', 'exec:migrate', 'exec:seed']);
  grunt.registerTask('test_chrome', ['test_setup', 'protractor:chrome']);
  grunt.registerTask('test_firefox', ['test_setup', 'protractor:firefox']);
  grunt.registerTask('test_ie', ['test_setup', 'protractor:ie']);
  grunt.registerTask('test_safari', ['test_setup', 'protractor:safari']);
};
