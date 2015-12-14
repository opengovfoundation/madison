var TEST_PORT = process.env.TRAVIS_JOB_NUMBER ? '80' : '8100';

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
    serve: {
      //cmd: 'DB_CONNECTION=mysql_testing php -S localhost:8100 -t ./public&',
      stdout: true,
      cmd: 'DB_CONNECTION=mysql_testing php artisan serve --port ' + TEST_PORT + '&',
      stderr: true
    },
    rebuild_db: {
      cmd: 'php artisan db:rebuild --database=mysql_testing'
    },
    migrate: {
      cmd: "php artisan migrate --database=mysql_testing"
    },
    seed: {
      cmd: "php artisan db:seed --database=mysql_testing"
    }
  });

  grunt.loadNpmTasks('grunt-exec');
};
