module.exports = function (grunt) {
  grunt.config.set('clean', {
    build: ['public/build/*.js']
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
};