/*global annotator*/
/*global Markdown*/
angular.module('madisonApp.controllers')
  .controller('DocumentPageController', ['$scope', '$state', '$timeout', 'growl', '$location', '$window', 'Doc', '$sce', '$stateParams', '$http', 'loginPopupService', 'annotationService', '$anchorScroll', 'AuthService',
    function ($scope, $state, $timeout, growl, $location, $window, Doc, $sce, $stateParams, $http, loginPopupService, annotationService, $anchorScroll, AuthService) {
      $scope.annotations = [];

      $scope.setSponsor = function () {
        try {
          //If the sponsor is a group
          if ($scope.doc.group_sponsor.length > 0) {
            $scope.doc.sponsor = $scope.doc.group_sponsor;
          } else if ($scope.doc.user_sponsor.length > 0) { //Otherwise it's an individual
            $scope.doc.sponsor = $scope.doc.user_sponsor;
            $scope.doc.sponsor[0].display_name = $scope.doc.sponsor[0].fname + ' ' + $scope.doc.sponsor[0].lname;
          } else { //This document has no sponsor!
            console.error("No sponsor found.");
          }
        } catch (err) {
          console.error(err);
        }
      };

      $scope.getSupported = function () {
        if ($scope.user) {
          $http.get('/api/users/' + $scope.user.id + '/support/' + $scope.doc.id)
            .success(function (data) {
              switch (data.support) {
              case "1":
                $scope.supported = true;
                break;
              case "":
                $scope.opposed = true;
                break;
              default:
                $scope.supported = null;
                $scope.opposed = null;
              }

              if ($scope.supported !== null && $scope.opposed !== null) {
                $('#doc-support').text(data.supports + ' Support');
                $('#doc-oppose').text(data.opposes + ' Oppose');
              }
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
              //Parse data to see what user's action is currently
              if (data.support === null) {
                $scope.supported = false;
                $scope.opposed = false;
              } else {
                $scope.supported = data.support;
                $scope.opposed = !data.support;
              }

              var button = $($event.target);
              var otherButton = $($event.target).siblings('a.btn');

              if (button.hasClass('doc-support')) {
                button.text(data.supports + ' Support');
                otherButton.text(data.opposes + ' Oppose');
              } else {
                button.text(data.opposes + ' Oppose');
                otherButton.text(data.supports + ' Support');
              }

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

      $scope.checkLoginPopupBinding = function () {
        //Check if the user is logged in
        if (!AuthService.isAuthenticated()) {
          $scope._bindLoginPopup();
        } else { //If the user isn't logged in, unbind mouseup
          angular.element('#doc_content').unbind('mouseup');
        }
      };

      $scope._bindLoginPopup = function () {
        angular.element('#doc_content').on('mouseup', function (event) {
          //Check for selection
          var selection = $window.getSelection();

          //If the selection is a range, show login popup
          if (selection.type === "Range") {
            loginPopupService.showLoginForm(event);
          }
        });
      };


      /**
      * Executed on controller initialization
      */

      //Load annotations
      $scope.$on('annotationsUpdated', function () {
        $scope.annotations = annotationService.annotations;
        $scope.$apply();

        //Check that we have a direct annotation link
        if ($location.$hash) {
          $scope.evalAsync(function () {
            $anchorScroll();
          });
        }
      });

      //Initial bind for login popup
      if (!AuthService.isAuthenticated()) {
        $scope._bindLoginPopup();
      }

      //Conditionally bind login popup on user authentication
      $scope.$on('sessionChanged', function () {
        $scope.checkLoginPopupBinding();
      });

      //Load the document
      $scope.doc = Doc.getDocBySlug({slug: $stateParams.slug});

      //After loading the document
      $scope.doc.$promise.then(function (doc) {

        $scope.setSponsor();
        $scope.getSupported();

        $scope.checkExists(doc);//Redirect if document doesn't exist

        //Load content.  Then attach annotator.
        $scope.loadContent(doc).then(function () {
          $scope.attachAnnotator($scope.doc, $scope.user);
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
          $scope.attachAnnotator($scope.doc, $scope.user);
        });

        $scope.doc.support_percent = 0;
        if(doc.support > 0) {
          $scope.doc.support_percent = Math.round( doc.support * 100 / (doc.support + doc.oppose) );
        }

        $scope.doc.oppose_percent = 0;
        if(doc.oppose > 0) {
          $scope.doc.oppose_percent = Math.round( doc.oppose * 100 / (doc.support + doc.oppose) );
        }

      });

    }]);
