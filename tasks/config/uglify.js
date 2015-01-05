module.exports = function (grunt) {
  grunt.config.set('uglify', {
    dist: {}
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
};