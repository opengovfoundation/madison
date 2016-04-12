angular.module('madisonApp.controllers')
  .controller('SiteSettingsController', function ($scope, $http, modalService,
    Doc, $translate, pageService, growl, growlMessages, SITE) {
    $translate('content.sitesettings.title', {title: SITE.name}).then(function(translation) {
      pageService.setTitle(translation);
    });

    var featuredDocs = Doc.getFeaturedDocs({'featured_only': true});
    $scope.resetting = false;
    $scope.featuredDocs = [];

    featuredDocs.$promise.then(function (data) {
      // Hack to cleanup our data object from Angular.
      data = angular.fromJson(angular.toJson(data));

      $scope.featuredDocs = data;

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
            closeButtonText: $translate.instant('form.general.cancel'),
            actionButtonText: $translate.instant('form.document.featured.change'),
            headerText: $translate.instant('form.document.featured.change'),
            bodyText: $translate.instant('form.document.featured.confirm')
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

    $scope.changeDocOrder = function (doc, oldOrder, direction) {
      var newOrder = oldOrder + direction;
      // Up, down, turn around
      if(newOrder > $scope.featuredDocs.length || newOrder < 0) {
        return false;
      }

      var oldDoc = $scope.featuredDocs[newOrder];
      $scope.featuredDocs[newOrder] = $scope.featuredDocs[oldOrder];
      $scope.featuredDocs[oldOrder] = oldDoc;

      var docOrder = [];
      for(var i in $scope.featuredDocs) {
        docOrder.push($scope.featuredDocs[i].id);
      }
      docOrder = docOrder.join(',');
      console.log(docOrder);

      $http.put('/api/docs/featured', {'docs' : docOrder})
        .then(function() {
          // growl.info($translate.instant('success.general.save'));
        },
        function(error) {
          console.log('error', error);
          growl.error($translate.instant('errors.general.save'));
        });
    };

    $scope.removeFeaturedDoc = function (doc) {
      $http.delete('/api/docs/featured/'+doc.id).then(function (data) {
        $scope.featuredDocs = data.data;
      });
    };
  });
