module.exports = function (grunt) {
  grunt.registerTask('buildJS', [
    'jshint', 'uglify', 'notify:uglify'
  ]);
};