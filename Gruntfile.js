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
      all: [
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
            'public/bower_components/bootstrap/dist/js/bootstrap.min.js',
            'public/bower_components/pagedown/Markdown.Converter.js',
            'public/bower_components/pagedown/Markdown.Sanitizer.js',
            'public/bower_components/pagedown/Markdown.Editor.js',
            'public/bower_components/crypto-js/index.js',
            'public/bower_components/google-translate/index.txt',
            'public/bower_components/bootstrap/js/collapse.js',
            'public/bower_components/bootstrap/js/modal.js',

            //Datetimepicker and dependencies
            'public/vendor/datetimepicker/datetimepicker.js',
            'public/bower_components/moment/min/moment.min.js',
            'public/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js',

            //Annotator JS
            'public/vendor/annotator/annotator-full.min.js',
            'public/vendor/showdown/showdown.js',
            'public/js/annotator-madison.js',

            //Custom JS
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
    watch: {
      scripts: {
        files: ['public/js/*.js', 'Gruntfile.js'],
        tasks: ['jshint', 'uglify']
      },
      sass: {
        files: './public/sass/**/*.scss',
        tasks: ['compass']
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
      dev: {
        options: {
          configFile: "protractor.conf.js"
        }
      }
    },
    connect: {
      server: {
        options: {
          hostname: 'local',
          port: 80,
          base: "public/",
          debug: true,
        }
      }
    }
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
  
  // Task definition
  grunt.registerTask('build', ['jshint', 'uglify', 'compass']);
  grunt.registerTask('default', ['jshint', 'uglify', 'watch']);
  grunt.registerTask('install', ['exec:install_composer', 'exec:install_bower']);
  grunt.registerTask('test_setup', ['exec:create_testdb', 'exec:migrate', 'exec:seed']);
};
