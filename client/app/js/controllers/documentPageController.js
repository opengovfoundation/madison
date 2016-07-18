/*global annotator*/
/*global Markdown*/
angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$state', '$timeout',
      'growl', '$location', '$window', 'Doc', '$sce', '$stateParams', '$http',
      'loginPopupService', 'annotationService', '$anchorScroll', 'AuthService',
      '$translate', 'pageService', 'SITE',
    function ($scope, $state, $timeout, growl, $location, $window, Doc, $sce,
      $stateParams, $http, loginPopupService, annotationService,
      $anchorScroll, AuthService, $translate, pageService, SITE) {

      $scope.annotations = [];
      $scope.activeTab = 'content';
      $scope.doc = {};
      $scope.currentPage = 1;
      $scope.loading = true;

      $scope.getSupported = function () {
        if ($scope.user) {
          $http.get('/api/users/' + $scope.user.id + '/support/' + $scope.doc.id)
            .success(function (data) {
              $scope._updateSupport(data);
            }).error(function () {
              console.error("Unable to get support info for user %o and doc %o", $scope.user, $scope.doc);
            });
        }
      };

      $scope.support = function (supported, $event) {
        if (!$scope.user) {
          loginPopupService.showLoginForm($event);
        } else {
          $http.post('/api/docs/' + $scope.doc.id + '/support', {
            'support': supported
          })
            .success(function (data) {
              $scope._updateSupport(data);
            })
            .error(function (data) {
              console.error("Error posting support: %o", data);
            });
        }
      };

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

      $scope.$on('docContentUpdated', function() {
        $scope.loading = false;
        // Wait for next tick so angular has digested content into page
        $timeout($anchorScroll);
      });

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
        var subCommentHash = $location.hash().match(/^annsubcomment_([0-9]+)$/);
        var annotationHash = $location.hash().match(/^annotation_([0-9]+)$/);
        var commentHash = $location.hash().match(/^comment_([0-9\-]+)$/);

        $scope.secondtab = false;

        // TODO: Once the hash for comments is decided, we can test for just
        // that and go to the comment tab, otherwise stay on content.
        if (commentHash) {
          $scope.secondtab = true;
          $scope.changeTab('comment');
        }
      };

      $scope.changeTab = function (activeTab) {
        $scope.activeTab = activeTab;
      };

      $scope.attachAnnotator = function (doc, user) {
        if (doc.discussion_state === 'hidden') return;

        //Grab the doc_content element that we want to attach Annotator to
        var element = $('#doc_content');

        //Use AnnotationService to create / store Annotator and annotations
        $timeout(function () {
          annotationService.createAnnotator(element, doc, user);
        });
      };

      $scope._updateSupport = function (data) {
        $scope.doc.support = data.supports;
        $scope.doc.oppose = data.opposes;
        $scope.voted = data.support === null ? false : true;

        $scope.$broadcast('supportUpdated');

        //Parse data to see what user's action is currently
        if (data.support === null) {
          $scope.supported = false;
          $scope.opposed = false;
        } else {
          $scope.supported = data.support;
          $scope.opposed = !data.support;
        }
      };

      $scope._calculateSupport = function () {
        $scope.doc.support_percent = 0;

        if ($scope.doc.support > 0) {
          $scope.doc.support_percent = Math.round($scope.doc.support * 100 / ($scope.doc.support + $scope.doc.oppose));
        }

        $scope.doc.oppose_percent = 0;
        if ($scope.doc.oppose > 0) {
          $scope.doc.oppose_percent = Math.round($scope.doc.oppose * 100 / ($scope.doc.support + $scope.doc.oppose));
        }
      };

      /**
      * Executed on controller initialization
      */

      //Load annotations
      $scope.$on('annotationsUpdated', function () {
        $scope.annotations = annotationService.annotations;

        //Check that we have a direct annotation link
        if ($location.$hash) {
          $scope.evalAsync(function () {
            $anchorScroll();
          });
        }
      });

      $scope.$on('supportUpdated', function () {
        $scope._calculateSupport();
      });

      //Load the document
      $scope.doc = Doc.get({id: $stateParams.slug});

      //After loading the document
      $scope.doc.$promise.then(function (doc) {

        $translate('content.document.title', {title: SITE.name, docTitle: doc.title}).then(function(translation) {
          pageService.setTitle(translation);
        });

        $scope.getSupported();

        $scope.checkExists(doc);//Redirect if document doesn't exist

        //Load content.  Then attach annotator.
        $scope.loadContent(doc).then(function () {
          $scope.attachAnnotator($scope.doc, $scope.user);
        });

        if(typeof $scope.doc.comments === 'undefined') {
          $scope.doc.comments = [];
        }

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
          $scope.attachAnnotator($scope.doc, $scope.user);
        });

        $scope._calculateSupport();

      });

    }]);
