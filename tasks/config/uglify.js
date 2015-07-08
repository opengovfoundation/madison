module.exports = function (grunt) {
  grunt.config.set('uglify', {
    dist: {},
    options: {
      mangle: false,
      sourceMap: true,
      preserveComments: false
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
};