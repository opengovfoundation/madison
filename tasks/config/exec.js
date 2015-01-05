module.exports = function (grunt) {
  grunt.config.set('exec', {
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
  });

  grunt.loadNpmTasks('grunt-exec');
};