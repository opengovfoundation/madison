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
  $translateProvider.registerAvailableLanguageKeys(['en', 'es', 'fr'], {
    'en_US': 'en',
    'en_UK': 'en',
    'es_CO': 'es'
  });
  $translateProvider.addInterpolation('$translateMessageFormatInterpolation');
  $translateProvider.useSanitizeValueStrategy('sanitizeParameters');
  $translateProvider.usePostCompiling(true);
  $translateProvider.uniformLanguageTag('java').determinePreferredLanguage();
  $translateProvider.fallbackLanguage('en');
});
