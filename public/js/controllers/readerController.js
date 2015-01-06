angular.module('madisonApp.controllers')
  .controller('ReaderController', ['$scope', '$http', 'annotationService', 'createLoginPopup', '$timeout', '$anchorScroll',
    function ($scope, $http, annotationService, createLoginPopup, $timeout, $anchorScroll) {
      $scope.annotations = [];

      $scope.$on('annotationsUpdated', function () {
        $scope.annotations = annotationService.annotations;
        $scope.$apply();

        $timeout(function () {
          $anchorScroll();
        }, 0);
      });

      $scope.init = function () {
        $scope.user = user;
        $scope.doc = doc;
        $scope.setSponsor();
        $scope.getSupported();
      };

      $scope.setSponsor = function () {
        try {
          if ($scope.doc.group_sponsor.length !== 0) {
            $scope.doc.sponsor = $scope.doc.group_sponsor;
          } else {
            $scope.doc.sponsor = $scope.doc.user_sponsor;
            $scope.doc.sponsor[0].display_name = $scope.doc.sponsor[0].fname + ' ' + $scope.doc.sponsor[0].lname;
          }
        } catch (err) {
          console.error(err);
        }
      };

      $scope.getSupported = function () {
        if ($scope.user.id !== '') {
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

        if ($scope.user.id === '') {
          createLoginPopup($event);
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
    }]);