module.exports = function (grunt) {
  grunt.registerTask('build', [
    'useminPrepare', 'buildJS', 'buildCSS', 'buildFilerev', 'usemin'
  ]);
};