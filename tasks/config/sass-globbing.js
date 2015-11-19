module.exports = function (grunt) {
  grunt.config.set('sass_globbing', {
    customStyles: {
      files: {
        'public/sass/_custom.scss': 'public/sass/custom/**/*.scss'
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass-globbing');
};
