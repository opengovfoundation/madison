/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('404Controller', ['$scope', '$stateParams', '$translate',
    'pageService', 'SITE',
    function ($scope, $stateParams, $translate, pageService, SITE) {
      $translate('content.404.title', { title: SITE.name })
      .then(function(translation) {

        pageService.setTitle(translation);
      });
    }
  ]);
