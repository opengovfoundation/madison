module.exports = function (grunt) {
  grunt.config.set('clean', {
    build: ['public/build/app.*.js']
  });

  grunt.loadNpmTasks('grunt-contrib-clean');
};