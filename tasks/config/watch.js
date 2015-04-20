module.exports = function (grunt) {
  grunt.config.set('watch', {
    scripts: {
      files: ['public/js/**/*.js', 'Gruntfile.js'],
      tasks: ['buildJS']
    },
    sass: {
      files: './public/sass/**/*.scss',
      tasks: ['buildCSS'],
      options: {
        livereload: true
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
};
