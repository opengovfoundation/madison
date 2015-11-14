angular.module('madisonApp.controllers')
  .controller('IntroPageController', ['$scope', '$translate', 'pageService',
    'SITE',
    function ($scope, $translate, pageService, SITE) {
      pageService.setTitle($translate.instant('content.intro.title',
        {title: SITE.name}));
      $scope.siteName = SITE.name;
    }
  ]);
