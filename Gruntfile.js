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
            'node_modules/angular/lib/angular.min.js',
            'node_modules/angular-bootstrap/ui-bootstrap.js',
            'node_modules/angular-animate/angular-animate.min.js',
            'public/bower_components/angular-cookies/angular-cookies.js',
            'public/bower_components/angular-ui/build/angular-ui.min.js',
            'public/bower_components/zeroclipboard/dist/ZeroClipboard.min.js',
            'public/bower_components/angular-growl/build/angular-growl.min.js',
            'node_modules/twitter-bootstrap-3.0.0/dist/js/bootstrap.min.js',

            //Datetimepicker and dependencies
            'public/vendor/datetimepicker/datetimepicker.js',
            'public/bower_components/moment/min/moment.min.js',

            'public/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js',
            'public/js/controllers.js',
            'public/js/dashboardControllers.js',
            'public/js/services.js',
            'public/js/directives.js',
            'public/js/filters.js',
            'public/js/annotationServiceGlobal.js',
            'public/js/app.js'
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
        files: './public/sass/**.scss',
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
      },
      codeception: {
        cmd: 'vendor/codeception/codeception/codecept build && vendor/codeception/codeception/codecept run'
      }
    },
    db_dump: {
      testing: {
        options: (function () {
          var creds = grunt.file.readYAML('codeception.yml');
          var returned = {
            title: "Dump for test suite",
            database: creds.modules.config.Db.dsn.split('=')[2],
            user: creds.modules.config.Db.user,
            pass: creds.modules.config.Db.password,
            host: creds.modules.config.Db.dsn.split('=')[1].replace(';', ''),
            backup_to: "tests/_data/dump.sql"
          };

          console.log(returned);

          return returned;
        }())
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
  grunt.registerTask('default', ['jshint', 'uglify', 'watch']);
  grunt.registerTask('install', ['exec:install_composer', 'exec:install_bower', 'exec:install_npm']);
  grunt.registerTask('test', ['db_dump:testing', 'exec:codeception']);
};
