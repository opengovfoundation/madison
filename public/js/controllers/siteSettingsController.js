angular.module('madisonApp.controllers')
  .controller('SiteSettingsController', function ($scope, $http, modalService, Doc) {
    var featuredDoc = Doc.getFeaturedDoc();
    $scope.resetting = false;

    featuredDoc.$promise.then(function () {
      $scope.currentFeatured = {
        id: featuredDoc.id,
        text: featuredDoc.title
      };

      $scope.$watch('currentFeatured', function (newValue, oldValue) {
        //Catch for resetting select2 after modal cancel
        if ($scope.resetting) {
          $scope.resetting = false;
          return;
        }

        //Workaround for angular initializing variable
        //If they're different we're changing the value.
        if (!angular.equals(newValue, oldValue)) {
          //Call modal for confirmation
          var modalOptions = {
            closeButtonText: 'Cancel',
            actionButtonText: 'Change Featured Document',
            headerText: 'Change Featured Document?',
            bodyText: 'Are you sure you want to change the featured document?'
          };

          //Open the dialog
          var res = modalService.showModal({}, modalOptions);

          //Reset featured document on cancel
          res.catch(function () {
            $scope.resetting = true;
            $scope.currentFeatured = {
              id: featuredDoc.id,
              text: featuredDoc.title
            };
          });

          //Set featured document on success
          res.then(function () {
            $scope._setFeaturedDoc($scope.currentFeatured.id);
          });
        }
      });
    });

    //Set select2 options
    $scope.featuredOptions = {
      minimumInputLength: 3,
      multiple: false,
      closeOnSelect: true,
      query: function (options) {
        var data = { results: [] };
        var i;

        //TODO: This should be called via the Doc resource
        $http.get('/api/docs/?title=' + options.term)
          .then(function (response) {
            for (i = 0; i < response.data.length; i++) {
              data.results.push({
                id: response.data[i].id,
                text: response.data[i].title
              });
            }
            options.callback(data);
          });
      }
    };

    //Make API call for setting featured document
    $scope._setFeaturedDoc = function (id) {
      return $http.post('/api/docs/featured', {id: id});
    };
  });
