module.exports = function (grunt) {
  grunt.registerTask('test_firefox', [
    'test_setup', 'protractor:firefox'
  ]);
};