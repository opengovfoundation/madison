// Don't use this task, use build instead.
// This task doesn't have bless working properly.
module.exports = function (grunt) {
  grunt.registerTask('buildCSS', [
    'sass_globbing', 'compass', 'cssmin', 'notify:cssmin'
  ]);
};
