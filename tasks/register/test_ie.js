module.exports = function (grunt) {
  grunt.registerTask('test_ie', [
    'test_setup', 'protractor:ie'
  ]);
};