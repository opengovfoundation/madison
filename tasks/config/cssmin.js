module.exports = function (grunt) {
  grunt.config.set('cssmin', {
    options: {
      processImport: true,
      keepSpecialComments: false,
      advanced: false
    },
    generated: {

        files: {
          'public/build/app.css': [
              'public/css/style.css',
              'public/bower_components/angular-tour/dist/angular-tour.css',
              'public/bower_components/angular-growl-v2/build/angular-growl.min.css',
              'public/vendor/pagedown/assets/demo.css',
              'public/vendor/datetimepicker/datetimepicker.css',
              'public/vendor/jquery/jquery-ui-smoothness.css',
              //'public/vendor/bootstrap/css/bootstrap.min.css',
              //'public/vendor/bootstrap/css/bootstrap-theme.min.css',
              'public/vendor/select2/select2.css',
              'public/vendor/annotator/annotator.min.css',
            ]
        }
      }

  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
};
