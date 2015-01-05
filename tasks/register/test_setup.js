module.exports = function (grunt) {
  grunt.registerTask('test_setup', [
    'exec:drop_testdb', 'exec:create_testdb', 'exec:migrate', 'exec:seed'
  ]);
};