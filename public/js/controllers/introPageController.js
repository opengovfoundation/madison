angular.module('madisonApp.controllers')
  .controller('IntroPageController', ['$scope', '$translate', 'pageService',
    'SITE',
    function ($scope, $translate, pageService, SITE) {
      $translate('content.intro.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });
      $scope.siteName = SITE.name;
    }
  ]);
