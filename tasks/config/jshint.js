module.exports = function (grunt) {
  grunt.config.set('jshint', {
    options: {
      proto: true
    },
    all: [
      'public/js/**/*.js',
    ]
  });

  grunt.loadNpmTasks('grunt-contrib-jshint');
};