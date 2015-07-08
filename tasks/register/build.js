module.exports = function (grunt) {
  grunt.registerTask('build', [
    'clean', 'jshint', 'useminPrepare', 'compass', 'cssmin', 'concat', 'uglify', 'copy', 'usemin', 'cacheBust'
  ]);
};
