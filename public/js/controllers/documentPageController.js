/*global annotator*/
/*global Markdown*/
angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$state', '$timeout', 'growl', '$location', '$window', 'Doc', '$sce', '$stateParams',
    function ($scope, $state, $timeout, growl, $location, $window, Doc, $sce, $stateParams) {
      //Load the document
      $scope.doc = Doc.getDocBySlug({slug: $stateParams.slug});

      //After loading the document
      $scope.doc.$promise.then(function (doc) {

        $scope.checkExists(doc);//Redirect if document doesn't exist

        //Load content.  Then attach annotator.
        $scope.loadContent(doc).then(function () {
          $scope.attachAnnotator($scope.doc, $scope.currentUser);
        });

        //Load introduction section from sponsor
        $scope.loadIntrotext(doc);//Load the document introduction text

        //Check if we're linking to the discussion tab
        $scope.checkActiveTab($scope.doc, $scope.user);

        /*jslint unparam: true*/
        $scope.$on('tocAdded', function (event, toc) {
          $scope.toc = toc;
        });
        /*jslint unparam: false*/

        //When the session is changed, re-attach annotator
        $scope.$on('sessionChanged', function () {
          $scope.attachAnnotator($scope.doc, $scope.currentUser);
        });
      });

      //Ensure that we actually get a document back from the server
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

        $scope.doc.content.$promise.then(function () {
          $scope.doc.html = $sce.trustAsHtml($scope.doc.content.html);
          $scope.$broadcast('docContentUpdated');//Broadcast that the body has been updated
        });

        return $scope.doc.content.$promise;
      };

      //Load the introtext if we have one
      $scope.loadIntrotext = function (doc) {
        //Set the document introtext
        if (doc.introtext) {
          var converter = new Markdown.Converter();
          $scope.introtext = $sce.trustAsHtml(converter.makeHtml(doc.introtext));
        }
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

        $window.doc = doc;
        $window.user = user;

        var userId = null;
        var readOnly = true;

        if (user) {
          userId = user.id;
          readOnly = false;
        }

        $timeout(function () {

          $window.annotator = $('#doc_content').annotator({
            readOnly: readOnly
          });

          $window.annotator.annotator('addPlugin', 'Unsupported');
          $window.annotator.annotator('addPlugin', 'Tags');
          $window.annotator.annotator('addPlugin', 'Markdown');
          $window.annotator.annotator('addPlugin', 'Store', {
            annotationData: {
              'uri': $location.path(),
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

          $window.annotator.annotator('addPlugin', 'Permissions', {
            user: user,
            permissions: {
              'read': [],
              'update': [userId],
              'delete': [userId],
              'admin': [userId]
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

          $window.annotator.annotator('addPlugin', 'Madison', {
            userId: userId
          });
        });
      };
    }]);