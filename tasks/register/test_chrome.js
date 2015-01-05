module.exports = function (grunt) {
  grunt.registerTask('test_chrome', [
    'test_setup', 'protractor:chrome'
  ]);
};