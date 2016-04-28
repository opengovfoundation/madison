angular.module('madisonApp.translate', ['pascalprecht.translate'])

.config(function ($translateProvider) {
  $translateProvider.useStaticFilesLoader({
    files: [
      {
        prefix: '/locales/',
        suffix: '.json'
      },
      {
        prefix: '/locales/custom/',
        suffix: '.json'
      }
    ]
  });
  $translateProvider.addInterpolation('$translateMessageFormatInterpolation');
  $translateProvider.useSanitizeValueStrategy('sanitizeParameters');
  $translateProvider.usePostCompiling(true);
  $translateProvider.use('en_US');
});
