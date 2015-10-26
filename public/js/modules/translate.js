'use strict';

angular.module('madisonApp.translate', ['pascalprecht.translate'])

.config(function ($translateProvider) {
  $translateProvider.useStaticFilesLoader({
    prefix: '/locales/',
    suffix: '.json'
  });
  $translateProvider.addInterpolation('$translateMessageFormatInterpolation');
  $translateProvider.useSanitizeValueStrategy('sanitizeParameters');
  $translateProvider.use('en_US');
});
