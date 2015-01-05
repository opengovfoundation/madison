module.exports = function (grunt) {
  grunt.registerTask('build', [
    'clean', 'jshint', 'useminPrepare', 'concat', 'uglify', 'copy' ,'filerev', 'usemin'
  ]);
};