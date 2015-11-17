/**
 * Generic content controller
 * Injects (translated) content into a generic template.
 */
angular.module('madisonApp.controllers')
  .controller('ContentController', ['$scope', '$stateParams', '$translate',
    'pageService', 'SITE',
    function ($scope, $stateParams, $translate, pageService, SITE) {
      var page = $stateParams.page.replace(/-/g, '');
      pageService.setTitle($translate.instant('content.' + page + '.title',
        {title: SITE.name}));
      $scope.header = $translate.instant('content.' + page + '.header',
        {title: SITE.name});
      $scope.body = $translate.instant('content.' + page + '.body',
        {title: SITE.name});
    }
  ]);
