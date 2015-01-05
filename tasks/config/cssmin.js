module.exports = function (grunt) {
  grunt.config.set('cssmin', {
    generated: {
      combine: {
        files: {
          'public/build/app.css': [
            'public/bower_components/angular-tour/dist/angular-tour.css',
            'public/bower_components/angular-growl/build/angular-growl.min.css',
            'public/vendor/pagedown/assets/demo.css',
            'public/vendor/datetimepicker/datetimepicker.css',
            'public/vendor/jquery/jquery-ui-smoothness.css',
            'public/vendor/bootstrap/css/bootstrap.min.css',
            'public/vendor/bootstrap/css/bootstrap-theme.min.css',
            'public/vendor/select2/select2.css',
            'public/vendor/annotator/annotator.min.css',
            'public/css/style.css',
            'public/css/dropdown-sub.css'
          ]
        }
      }
    }
  });

  grunt.loadNpmTasks('grunt-contrib-cssmin');
};