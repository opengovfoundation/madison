module.exports = function (grunt) {
  grunt.registerTask('buildCSS', [
    'compass', 'cssmin', 'notify:cssmin'
  ]);
};