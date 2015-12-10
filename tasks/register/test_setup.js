module.exports = function (grunt) {
  grunt.registerTask('test_setup', [
    'exec:rebuild_db', 'exec:migrate', 'exec:seed', 'exec:serve'
  ]);
};
