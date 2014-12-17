angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$cookies', '$location', 'Doc', '$sce', '$stateParams',
    function ($scope, $cookies, $location, Doc, $sce, $stateParams) {
      console.log($stateparams);
      $scope.hideIntro = $cookies.hideIntro;

      // Check which tab needs to be active - if the location hash
      // is #annsubcomment or there is no hash, the annotation/bill tab needs to be active
      // Otherwise, the hash is #subcomment/#comment and the discussion tab should be active
      var annotationHash = $location.hash().match(/^annsubcomment_([0-9]+)$/);
      $scope.secondtab = false;
      if (!annotationHash && ($location.hash())) {
        $scope.secondtab = true;
      }

      $scope.hideHowToAnnotate = function () {
        $cookies.hideIntro = true;
        $scope.hideIntro = true;
      };

      $scope.doc = Doc.get({id: doc.id}, function () {

        //If intro text exists, convert & trust the markdown content
        if (undefined !== $scope.doc.introtext[0]) {
          var converter = new Markdown.Converter();

          $scope.introtext = $sce.trustAsHtml(converter.makeHtml($scope.doc.introtext[0].meta_value));
        }
      });
    }
    ]);