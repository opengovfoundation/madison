module.exports = function (grunt) {
  grunt.registerTask('test_safari', [
    'test_setup', 'protractor:safari'
  ]);
};