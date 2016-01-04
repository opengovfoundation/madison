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
      cmd: 'TESTING=true DB_CONNECTION=mysql_testing php artisan serve --host 0.0.0.0 --port 8100&',
      // Silence these so they don't clutter up the build logs
      stdout: false,
      stderr: false
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
