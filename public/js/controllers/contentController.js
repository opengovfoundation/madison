/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('ContentController', ['$scope', '$stateParams', '$translate',
    'pageService', 'SITE', 'Page', 'page',
    function ($scope, $stateParams, $translate, pageService, SITE, Page, page) {

      $scope.page = page;

      pageService.setTitle(SITE.name + ' - ' + page.page_title);

      Page.getContent({ id: page.id }, function(content) {
        $scope.page.content = content.content;
      }, function(err) {
        $translate('errors.general.load').then(function(translation) {
          growl.error(translation);
        });
      });
    }
  ]);
