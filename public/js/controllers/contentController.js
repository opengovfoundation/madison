/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('ContentController', ['$scope', '$stateParams', '$translate',
    '$sce', 'pageService', 'SITE',
    function ($scope, $stateParams, $translate, $sce, pageService, SITE) {
      var page = $stateParams.page.replace(/-/g, '');
      $translate('content.' + page + '.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });
      $translate('content.' + page + '.header', {title: SITE.name}).then(function(translation) {
        $scope.header = translation;
      });
      $translate('content.' + page + '.body', {title: SITE.name}).then(function(translation) {
        $scope.body = $sce.trustAsHtml(translation);
      });
    }
  ]);
