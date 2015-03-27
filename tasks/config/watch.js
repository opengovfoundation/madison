module.exports = function (grunt) {
  grunt.config.set('watch', {
    scripts: {
      files: ['public/js/**/*.js', 'Gruntfile.js'],
      tasks: ['build']
    },
    sass: {
      files: './public/sass/**/*.scss',
      tasks: ['build']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
};
