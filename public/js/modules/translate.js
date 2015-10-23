'use strict';

angular.module('madisonApp.translate', ['pascalprecht.translate'])

.config(function ($translateProvider) {
  $translateProvider.useStaticFilesLoader({
    prefix: '/locales/',
    suffix: '.json'
  });
  $translateProvider.addInterpolation('$translateMessageFormatInterpolation');
  $translateProvider.use('en_US');
});
