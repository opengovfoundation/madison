/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('ContentController', ['$scope', '$stateParams', '$translate',
    'pageService', 'SITE', 'page', 'pageContent',
    function ($scope, $stateParams, $translate, pageService, SITE, page, pageContent) {
      pageService.setTitle(SITE.name + ' - ' + page.page_title);
      page.content = pageContent.content
      $scope.page = page;
    }
  ]);
