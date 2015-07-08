module.exports = function (grunt) {
  grunt.config.set('notify', {
    uglify: {
      options: {
        message: 'Uglify complete.'
      }
    },
    cssmin: {
      options: {
        message: 'CSSmin complete.'
      }
    },
    filerev: {
      options: {
        message: 'Filerev complete.'
      }
    }
  });

  grunt.loadNpmTasks('grunt-notify');
};