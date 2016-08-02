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
        var id = $location.hash();

        if (!id) {
          return;
        }

        // if any of these match, we want to lookup in our existing set of
        // comments/annotations if the old id matches to any of them and get the
        // new id for use later
        var subCommentHash = $location.hash().match(ANNOTATION_COMMENT_HASH_REGEX);
        var annotationHash = $location.hash().match(ANNOTATION_HASH_REGEX);
        var commentHash = $location.hash().match(COMMENT_HASH_REGEX);
        var comment;

        if (commentHash) {
          comment = _.find($scope.doc.comments, function (item) {
            return parseInt(item.old_id) === parseInt(commentHash[1]);
          });
        } else if (annotationHash) {
          comment = _.find($scope.annotations, function (item) {
            return parseInt(item.old_id) === parseInt(annotationHash[1]);
          });
        } else if (subCommentHash) {
          comment = _.find($scope.doc.comments, function (item) {
            return parseInt(item.old_id) === parseInt(subCommentHash[1]);
          })
          // TODO: the comment we want is a child of this item, so fetch the
          // comments that have this as it's parent and iterate again to
          // find the one that matches the id
          // TODO: since we are already grabbing the info, push it onto the
          // comments array?
        }

        if (comment) {
          id = comment.id;
        }

        // If the element doesn't exist then either the hash is just wrong, or
        // it belongs to content that isn't loaded yet
        if (!angular.element('document').find(id).length) {
          // TODO: figure out what the id belongs to (most likely a subcomment
          // at this point, but could be a paragraph on another page)
        }

        if (angular.element('#tab-discussion').find(id).length) {
          // Comments tab
          $scope.changeTab('comment');
          // TODO: trigger anchorScroll? leave it up to the comment controller
          // as it is now?
        } else if (angular.element('.annotation-container').find(id).length) {
          // Notes pane
          // TODO open the pane and scroll? leave it up to the annotation
          // controller as it is now?
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
