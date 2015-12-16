module.exports = function (grunt) {
  grunt.registerTask('test_setup', [
    'exec:serve', 'exec:rebuild_db', 'exec:migrate', 'exec:seed'
  ]);
};
