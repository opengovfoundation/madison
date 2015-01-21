/*global annotator*/
/*global Markdown*/
angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$state', 'growl', 'ipCookie', '$location', 'Doc', '$sce', '$stateParams',
    function ($scope, $state, growl, ipCookie, $location, Doc, $sce, $stateParams) {
      //Load the document
      $scope.doc = Doc.getDocBySlug({slug: $stateParams.slug});

      //After loading the document
      $scope.doc.$promise.then(function (doc) {
        $scope.checkExists(doc);//Redirect if document doesn't exist
        $scope.loadContent(doc);//Load document body
        $scope.$broadcast('docContentUpdated');//Broadcast that the body has been updated
        $scope.loadIntrotext(doc);//Load the document introduction text
        $scope.hideIntro = ipCookie('hideIntro');//Check the hideIntro cookie for the introduction gif
        $scope.checkActiveTab($scope.doc, $scope.user);

      });

      $scope.checkExists = function (doc) {
        //This document does not exist, redirect home
        if (!doc.id) {
          growl.error('That document does not exist!');
          $state.go('index');
        }
      };

      $scope.loadContent = function (doc) {
        //Set the document content
        $scope.doc.content = Doc.getDocContent({id: doc.id});
        $scope.doc.html = $sce.trustAsHtml($scope.doc.content.html);
      };

      $scope.loadIntrotext = function (doc) {
        //Set the document introtext
        var converter = new Markdown.Converter();
        $scope.introtext = $sce.trustAsHtml(converter.makeHtml(doc.introtext));
      };

      $scope.hideHowToAnnotate = function () {
        ipCookie('hideIntro', true);
        $scope.hideIntro = true;
      };

      $scope.checkActiveTab = function () {
        // Check which tab needs to be active - if the location hash
        // is #annsubcomment or there is no hash, the annotation/bill tab needs to be active
        // Otherwise, the hash is #subcomment/#comment and the discussion tab should be active
        var annotationHash = $location.hash().match(/^annsubcomment_([0-9]+)$/);
        $scope.secondtab = false;
        if (!annotationHash && ($location.hash())) {
          $scope.secondtab = true;
        }
      };


      $scope.attachAnnotator = function (doc, user) {
        annotator = $('#doc_content').annotator({
          //readOnly: user.id == ''
        });

        annotator.annotator('addPlugin', 'Unsupported');
        annotator.annotator('addPlugin', 'Tags');
        annotator.annotator('addPlugin', 'Markdown');
        annotator.annotator('addPlugin', 'Store', {
          annotationData: {
            'uri': window.location.pathname,
            'comments': []
          },
          prefix: '/api/docs/' + doc.id + '/annotations',
          urls: {
            create: '',
            read: '/:id',
            update: '/:id',
            destroy: '/:id',
            search: '/search'
          }
        });

        annotator.annotator('addPlugin', 'Permissions', {
          user: user,
          permissions: {
            'read': [],
            'update': [user.id],
            'delete': [user.id],
            'admin': [user.id]
          },
          showViewPermissionsCheckbox: false,
          showEditPermissionsCheckbox: false,
          userId: function (user) {
            if (user && user.id) {
              return user.id;
            }

            return user;
          },
          userString: function (user) {
            if (user && user.name) {
              return user.name;
            }

            return user;
          }
        });

        annotator.annotator('addPlugin', 'Madison', {
          userId: user.id
        });
      };

      /**
      * Copied from doc.js
      *   Needs to be cleaned up!
      */
      $('.affix-elm').each(function (i, elm) {
        elm = $(elm);
        var elmtop = 0;
        if (elm.data('offset-top')) {
          elmtop = elm.data('offset-top');
        }
        var elmbottom = 0;
        if (elm.data('offset-bottom')) {
          elmbottom = elm.data('offset-bottom');
        }

        elm.affix({
          offset: {
            top: elmtop,
            bottom: elmbottom
          }
        });
      });
    }]);