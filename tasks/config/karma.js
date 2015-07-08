module.exports = function (grunt) {
  grunt.config.set('karma', {
    unit: {
      configFile: 'karma.conf.js'
    }
  });

  grunt.loadNpmTasks('grunt-karma');
};