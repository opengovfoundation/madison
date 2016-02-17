module.exports = function (grunt) {
  grunt.registerTask('build', [
    'clean', 'jshint', 'useminPrepare', 'sass_globbing', 'compass', 'cssmin', 'concat', 'uglify', 'copy', 'usemin', 'cacheBust'
  ]);
};
