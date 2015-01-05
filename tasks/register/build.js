module.exports = function (grunt) {
  grunt.registerTask('build', [
    'clean', 'jshint', 'useminPrepare', 'cssmin', 'concat', 'uglify', 'copy' ,'filerev', 'usemin'
  ]);
};