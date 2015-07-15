angular.module('madisonApp.controllers')
  .controller('HomePageController', ['$scope', '$filter', '$sce', 'Doc',
    function ($scope, $filter, $sce, Doc) {
      $scope.docs = [];
      $scope.featured = {};
      $scope.mostActive = [];
      $scope.mostRecent = [];
      $scope.categories = [];
      $scope.sponsors = [];
      $scope.statuses = [];
      $scope.dates = [];
      $scope.dateSort = '';
      $scope.select2 = '';
      $scope.docSort = "created_at";
      $scope.reverse = true;
      $scope.startStep = 0;

      // Retrieve all docs
      Doc.query({
          'order': $scope.docSort,
          'order_dir': ($scope.reverse ? 'DESC' : 'ASC')
      },function (data) {
        $scope.parseDocs(data);
      }).$promise.catch(function (data) {
        console.error("Unable to get documents: %o", data);
      });

      Doc.getFeaturedDoc(function (data) {
        $scope.featured = data;

        //Parse introtext if it exists
        if(!!data.introtext) {
          var converter = new Markdown.Converter();
          $scope.featured.introtext = $sce.trustAsHtml(converter.makeHtml(data.introtext));
        }
      }).$promise.catch(function (data) {
        console.error("Unable to get featured document: %o", data);
      });

      // Get the most recent docs
      Doc.query(
        {
          'order': 'updated_at',
          'order_dir': 'DESC',
          'limit': 6
        },
        function (data) {
          $scope.mostRecent = data;
        }
      ).$promise.catch(function (data) {
        console.error("Unable to get documents: %o", data);
      });

      // Get the most active docs
      Doc.query(
        {
          'order': 'activity',
          'order_dir': 'DESC',
          'limit': 6
        },
        function (data) {
          $scope.mostActive = data;
        }
      ).$promise.catch(function (data) {
        console.error("Unable to get documents: %o", data);
      });

      $scope.select2Config = {
        multiple: true,
        allowClear: true,
        placeholder: "Filter documents by category, sponsor, or status"
      };

      $scope.dateSortConfig = {
        allowClear: true,
        placeholder: "Sort By Date"
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
        if (collection.length === 0) {
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
