/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('ContentController', ['$scope', '$stateParams', '$translate',
    'pageService', 'SITE', 'Page', 'thePage',
    function ($scope, $stateParams, $translate, pageService, SITE, Page, thePage) {

      $scope.page = thePage;

      pageService.setTitle(SITE.name + ' - ' + $scope.page.page_title);

      Page.getContent({
        id: $scope.page.id,
        format: 'html'
      }, function(content) {
        $scope.page.content = content.content;
      }, function(err) {
        $translate('errors.general.load').then(function(translation) {
          growl.error(translation);
        });
      });
    }
  ]);
