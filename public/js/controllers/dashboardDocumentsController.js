angular.module('madisonApp.controllers')
  .controller('DashboardDocumentsController', ['$scope', '$http', '$filter', 'growl', '$state',
    function ($scope, $http, $filter, growl, $state) {
      $scope.docs = [];
      $scope.categories = [];
      $scope.sponsors = [];
      $scope.statuses = [];
      $scope.dates = [];
      $scope.dateSort = '';
      $scope.select2 = '';
      $scope.docSort = "created_at";
      $scope.reverse = true;

      $scope.select2Config = {
        multiple: true,
        allowClear: true,
        placeholder: "Filter documents by category, sponsor, or status"
      };

      $scope.dateSortConfig = {
        allowClear: true,
        placeholder: "Sort By Date"
      };

      //Retrieve all docs
      $http.get('/api/docs')
        .success(function (data) {
          $scope.parseDocs(data);
        })
        .error(function (data) {
          console.error("Unable to get documents: %o", data);
        });

      $scope.createDocument = function () {
        var title = $scope.newDocTitle;

        if (!title || !title.trim()) {
          growl.error('You must enter a document title to create a new document.');
        }

        $http.post('/api/docs', {title: title})
          .success(function (data) {
            console.log('Redirect to new document');
            //$state.go('edit-doc', {id: data.id});
          })
          .error(function () {
            $scope.newDocTitle = null;
          });
      };

      $scope.parseDocs = function (docs) {
        angular.forEach(docs, function (doc) {
          $scope.docs.push(doc);

          $scope.parseDocMeta(doc.categories, 'categories');
          $scope.parseDocMeta(doc.sponsor, 'sponsors');
          $scope.parseDocMeta(doc.statuses, 'statuses');

          angular.forEach(doc.dates, function (date) {
            date.date = Date.parse(date.date);
          });
        });
      };

      $scope.parseDocMeta = function (collection, name) {
        if (collection === undefined || collection.length === 0) {
          return;
        }

        angular.forEach(collection, function (item) {
          var found = $filter('getById')($scope[name], item.id);

          if (found === null) {
            switch (name) {
            case 'categories':
              $scope.categories.push(item);
              break;
            case 'sponsors':
              $scope.sponsors.push(item);
              break;
            case 'statuses':
              $scope.statuses.push(item);
              break;
            default:
              console.error('Unknown meta name: ' + name);
            }
          }
        });
      };

      $scope.docFilter = function (doc) {

        var show = false;

        if ($scope.select2 !== undefined && $scope.select2 !== '') {
          var cont = true;

          var select2 = $scope.select2.split('_');
          var type = select2[0];
          var value = parseInt(select2[1], 10);

          switch (type) {
          case 'category':
            angular.forEach(doc.categories, function (category) {
              if (+category.id === value && cont) {
                show = true;
                cont = false;
              }
            });
            break;
          case 'sponsor':
            angular.forEach(doc.sponsor, function (sponsor) {
              if (+sponsor.id === value && cont) {
                show = true;
                cont = false;
              }
            });
            break;
          case 'status':
            angular.forEach(doc.statuses, function (status) {
              if (+status.id === value && cont) {
                show = true;
                cont = false;
              }
            });
            break;
          }
        } else {
          show = true;
        }

        return show;
      };
    }
    ]);
