module.exports = function (grunt) {
  grunt.config.set('uglify', {
    dist: {},
    options: {
      mangle: false
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
};