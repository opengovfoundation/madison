module.exports = function (grunt) {
  grunt.config.set('uglify', {
    generated: {
      files: [{
        dest: 'public/build/app.js',
        src: [
          'public/vendor/jquery/jquery-1.10.2.js',
          'public/vendor/select2/select2.js',
          'public/vendor/underscore.min.js',
          'public/bower_components/google-diff-match-patch-js/diff_match_patch.js',
          'public/bower_components/angular/angular.min.js',
          'public/bower_components/angular-animate/angular-animate.min.js',
          'public/bower_components/angular-bootstrap/ui-bootstrap.min.js',
          'public/bower_components/angular-bootstrap/ui-bootstrap-tpls.min.js',
          'public/bower_components/angular-cookies/angular-cookies.js',
          'public/bower_components/angular-ui/build/angular-ui.min.js',
          'public/bower_components/zeroclipboard/dist/ZeroClipboard.min.js',
          'public/bower_components/angular-growl/build/angular-growl.min.js',
          'public/bower_components/angular-sanitize/angular-sanitize.js',
          'public/bower_components/angular-resource/angular-resource.min.js',
          'public/bower_components/angular-route/angular-route.min.js',
          'public/bower_components/bootstrap/dist/js/bootstrap.min.js',
          'public/bower_components/pagedown/Markdown.Converter.js',
          'public/bower_components/pagedown/Markdown.Sanitizer.js',
          'public/bower_components/pagedown/Markdown.Editor.js',
          'public/bower_components/crypto-js/index.js',
          'public/bower_components/google-translate/index.txt',
          'public/bower_components/bootstrap/js/collapse.js',
          'public/bower_components/bootstrap/js/modal.js',
          'public/bower_components/angular-tour/dist/angular-tour.min.js',
          'public/bower_components/angular-tour/dist/angular-tour-tpls.min.js',
          'public/bower_components/angular-cookie/angular-cookie.min.js',
          'public/bower_components/angular-ui-router/release/angular-ui-router.min.js',

          //Datetimepicker and dependencies
          'public/vendor/datetimepicker/datetimepicker.js',
          'public/bower_components/moment/min/moment.min.js',
          'public/bower_components/angular-bootstrap-datetimepicker/src/js/datetimepicker.js',

          //Annotator JS
          'public/vendor/annotator/annotator-full.min.js',
          'public/vendor/showdown/showdown.js',
          'public/js/annotator-madison.js',

          //Custom JS
          'public/js/bootstrap-tour.js',
            //Controllers
            'public/js/controllers.js',
            'public/js/controllers/passwordResetController.js',
          'public/js/resources.js',
          'public/js/dashboardControllers.js',
          'public/js/services.js',
          'public/js/directives.js',
          'public/js/filters.js',
          'public/js/annotationServiceGlobal.js',
          'public/js/app.js',
          'public/js/googletranslate.js'
        ]
      }]
    },
    options: {
      mangle: false
    }
  });

  grunt.loadNpmTasks('grunt-contrib-uglify');
};