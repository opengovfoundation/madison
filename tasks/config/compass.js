module.exports = function (grunt) {
  grunt.config.set('compass', {
    dist: {
      options: {
        config: './config.rb',
        environment: 'production'
      }
    },
    dev: {
      options: {
        config: './config.rb'
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-compass');
};