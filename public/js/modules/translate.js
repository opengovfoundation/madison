angular.module('madisonApp.translate', ['pascalprecht.translate'])

.config(function ($translateProvider) {
  $translateProvider.useStaticFilesLoader({
    prefix: '/bower_components/madison-locales/locales/',
    suffix: '.json'
  });
  $translateProvider.addInterpolation('$translateMessageFormatInterpolation');
  $translateProvider.useSanitizeValueStrategy('sanitizeParameters');
  $translateProvider.usePostCompiling(true);
  $translateProvider.use('en_US');
});
