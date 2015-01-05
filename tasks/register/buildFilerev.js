module.exports = function (grunt) {
  grunt.registerTask('buildFilerev', [
    'filerev', 'notify:filerev'
  ]);
};