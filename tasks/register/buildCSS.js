module.exports = function (grunt) {
  grunt.registerTask('buildCSS', [
    'sass_globbing', 'compass', 'cssmin', 'notify:cssmin'
  ]);
};
