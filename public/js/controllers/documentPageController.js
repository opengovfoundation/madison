angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$state', 'growl', 'ipCookie', '$location', 'Doc', '$sce', '$stateParams',
    function ($scope, $state, growl, ipCookie, $location, Doc, $sce, $stateParams) {
      $scope.doc = Doc.getDocBySlug({slug: $stateParams.slug});

      $scope.doc.$promise.then(function (doc) {
        //This document does not exist
        if (!doc.id) {
          growl.error('That document does not exist!');
          $state.go('index');
        }

        $scope.doc.content = Doc.getDocContent({id: doc.id});
        $scope.doc.content.html = $sce.trustAsHtml($scope.doc.content.html);
      });

      $scope.hideIntro = ipCookie('hideIntro');

      // Check which tab needs to be active - if the location hash
      // is #annsubcomment or there is no hash, the annotation/bill tab needs to be active
      // Otherwise, the hash is #subcomment/#comment and the discussion tab should be active
      var annotationHash = $location.hash().match(/^annsubcomment_([0-9]+)$/);
      $scope.secondtab = false;
      if (!annotationHash && ($location.hash())) {
        $scope.secondtab = true;
      }

      $scope.hideHowToAnnotate = function () {
        ipCookie('hideIntro', true);
        $scope.hideIntro = true;
      };

      // $scope.doc = Doc.get({id: doc.id}, function () {

      //   //If intro text exists, convert & trust the markdown content
      //   if (undefined !== $scope.doc.introtext[0]) {
      //     var converter = new Markdown.Converter();

      //     $scope.introtext = $sce.trustAsHtml(converter.makeHtml($scope.doc.introtext[0].meta_value));
      //   }
      // });
    }]);