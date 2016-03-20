module.exports = function (grunt) {
  grunt.config.set('watch', {
    // scripts: {
    //   files: ['public/js/**/*.js', 'Gruntfile.js'],
    //   tasks: ['buildJS']
    // },
    // sass: {
    //   files: './public/sass/**/*.scss',
    //   tasks: ['buildCSS'],
    //   options: {
    //     livereload: true
    //   }
    // },
    assets: {
      files: ['public/js/**/*.js', 'Gruntfile.js', './public/sass/**/*.scss'],
      tasks: ['build']
    },
    tests: {
      files: './tests/client/**/*.js',
      tasks: ['test_chrome']
    }
  });

  grunt.loadNpmTasks('grunt-contrib-watch');
};
