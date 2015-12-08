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
    rebuild_db: {
      cmd: 'php artisan db:rebuild --database=grunt_test'
    },
    migrate: {
      cmd: "php artisan migrate --database=grunt_test"
    },
    seed: {
      cmd: "php artisan db:seed --database=grunt_test"
    }
  });

  grunt.loadNpmTasks('grunt-exec');
};
