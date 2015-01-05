module.exports = function (grunt) {
  grunt.config.set('concat', {
    options: {
      separator: grunt.util.linefeed + ';' + grunt.util.linefeed
    },
    dist: {}
  });

  grunt.loadNpmTasks('grunt-contrib-concat');
};