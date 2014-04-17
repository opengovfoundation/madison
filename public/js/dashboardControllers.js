/*global Markdown*/
/*global clean_slug*/
angular.module('madisonApp.dashboardControllers', [])
  .controller('DashboardVerifyController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.requests = [];

      $scope.init = function () {
        $scope.getRequests();
      };

      $scope.getRequests = function () {
        $http.get('/api/user/verify')
          .success(function (data) {
            $scope.requests = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.update = function (request, status) {
        $http.post('/api/user/verify', {'request': request, 'status': status})
          .success(function () {
            request.meta_value = status;
          })
          .error(function (data) {
            console.error(data);
          });
      };
    }
    ])
  .controller('DashboardSettingsController', ['$scope', '$http',
    function ($scope, $http) {
      $scope.admins = [];

      $scope.getAdmins = function () {
        $http.get('/api/user/admin')
          .success(function (data) {
            $scope.admins = data;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.saveAdmin = function (admin) {
        admin.saved = false;

        $http.post('/api/user/admin', {'admin': admin})
          .success(function () {
            admin.saved = true;
          })
          .error(function (data) {
            console.error(data);
          });
      };

      $scope.init = function () {
        $scope.getAdmins();
      };
    }
    ])
  .controller('DashboardEditorController', ['$scope', '$http', '$timeout', '$location', '$filter',
    function ($scope, $http, $timeout, $location, $filter) {
      $scope.doc = {};
      $scope.sponsor = {};
      $scope.status = {};
      $scope.newdate = {label: '', date: new Date()};
      $scope.verifiedUsers = [];
      $scope.categories = [];
      $scope.suggestedCategories = [];
      $scope.suggestedStatuses = [];
      $scope.dates = [];

      $scope.init = function () {
        var abs = $location.absUrl();
        var id = abs.match(/.*\/(\d+)$/)[1];

        var docDone = $scope.getDoc(id);

        $scope.getAllCategories();
        $scope.getVerifiedUsers();
        $scope.setSelectOptions();

        var initCategories = true;
        var initSponsor = true;
        var initStatus = true;
        var initDoc = true;

        docDone.then(function () {
          new Markdown.Editor(Markdown.getSanitizingConverter()).run();

          $scope.getDocSponsor().then(function () {
            $scope.$watch('sponsor', function () {
              if (initSponsor) {
                $timeout(function () { initSponsor = false; });
              } else {
                $scope.saveSponsor();
              }
            });
          });

          $scope.getDocStatus().then(function () {
            $scope.$watch('status', function () {
              if (initStatus) {
                $timeout(function () { initStatus = false; });
              } else {
                $scope.saveStatus();
              }
            });
          });

          $scope.getDocCategories().then(function () {
            $scope.$watch('categories', function () {
              if (initCategories) {
                $timeout(function () { initCategories = false; });
              } else {
                $scope.saveCategories();
              }
            });
          });

          $scope.getDocDates();

          $scope.$watchCollection('[doc.slug, doc.title, doc.content.content]', function () {
            if (initDoc) {
              $timeout(function () { initDoc = false; });
            } else {
              $scope.doc.slug = clean_slug($scope.doc.slug);
              $scope.saveDoc();
            }
          });
        });
      };

      $scope.setSelectOptions = function () {
        $scope.categoryOptions = {
          multiple: true,
          simple_tags: true,
          tags: function () {
            return $scope.suggestedCategories;
          },
          results: function () {
            return $scope.categories;
          },
          initSelection: true
        };

        /*jslint unparam: true*/
        $scope.statusOptions = {
          placeholder: "Select Document Status",
          data: function () {
            return $scope.suggestedStatuses;
          },
          results: function () {
            console.log($scope.status, "Scope status");
            return $scope.status;
          },
          createSearchChoice: function (term) {
            return { id: term, text: term};
          },
          initSelection: function (element, callback) {
            callback(angular.copy($scope.status));
          }
        };

        $scope.sponsorOptions = {
          placeholder: "Select Document Sponsor",
          ajax: {
            url: "/api/user/verify",
            dataType: 'json',
            data: function () {
              return;
            },
            results: function (data) {
              var returned = [];
              angular.forEach(data, function (verified) {
                var text = verified.user.fname + " " + verified.user.lname + " - " + verified.user.email;

                returned.push({ id: verified.user.id, text: text });
              });

              return {results: returned};
            }
          },
          initSelection: function (element, callback) {
            callback($scope.sponsor);
          }
        };
        /*jslint unparam: false*/
      };

      $scope.statusChange = function (status) {
        $scope.status = status;
      };

      $scope.sponsorChange = function (sponsor) {
        $scope.sponsor = sponsor;
      };

      $scope.categoriesChange = function (categories) {
        $scope.categories = categories;
      };

      $scope.getDoc = function (id) {
        return $http.get('/api/docs/' + id)
          .success(function (data) {
            $scope.doc = data;
          });
      };

      $scope.saveDoc = function () {
        return $http.post('/api/docs/' + $scope.doc.id, $scope.doc)
          .success(function (data) {
            console.log("Document saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving categories for document %o: %o \n %o", $scope.doc, $scope.categories, data);
          });
      };

      $scope.createDate = function (newDate) {
        if ($scope.newdate.label !== '') {
          $scope.newdate.date = $filter('date')(newDate, 'short');

          $http.post('/api/docs/' + $scope.doc.id + '/dates', {date: $scope.newdate})
            .success(function (data) {
              data.date = Date.parse(data.date);
              data.$changed = false;
              $scope.dates.push(data);

              $scope.newdate = {label: '', date: new Date()};
            }).error(function (data) {
              console.error("Unable to save date: %o", data);
            });
        }
      };

      $scope.deleteDate = function (date) {
        $http.delete('/api/docs/' + $scope.doc.id + '/dates/' + date.id)
          .success(function () {
            var index = $scope.dates.indexOf(date);
            $scope.dates.splice(index, 1);
          }).error(function () {
            console.error("Unable to delete date: %o", date);
          });
      };

      $scope.saveDate = function (date) {
        var sendDate = angular.copy(date);
        sendDate.date = $filter('date')(sendDate.date, 'short');

        return $http.put('/api/dates/' + date.id, {date: sendDate})
          .success(function (data) {
            date.$changed = false;
            console.log("Date saved successfully: %o", data);
          }).error(function (data) {
            console.error("Unable to save date: %o (%o)", date, data);
          });
      };

      $scope.getDocDates = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/dates')
          .success(function (data) {
            angular.forEach(data, function (date, index) {
              date.date = Date.parse(date.date);
              date.$changed = false;
              $scope.dates.push(angular.copy(date));

              $scope.$watch('dates[' + index + ']', function (newitem, olditem) {
                if (!angular.equals(newitem, olditem) && newitem !== undefined) {
                  newitem.$changed = true;
                }
              }, true);
            });
          }).error(function (data) {
            console.error("Error getting dates: %o", data);
          });
      };

      $scope.getVerifiedUsers = function () {
        return $http.get('/api/user/verify')
          .success(function (data) {
            angular.forEach(data, function (verified) {
              $scope.verifiedUsers.push(angular.copy(verified.user));
            });
          }).error(function (data) {
            console.error("Unable to get verified users: %o", data);
          });
      };

      $scope.getDocCategories = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/categories')
          .success(function (data) {
            angular.forEach(data, function (category) {
              $scope.categories.push(category.name);
            });
          }).error(function (data) {
            console.error("Unable to get categories for document %o: %o", $scope.doc, data);
          });
      };

      $scope.getDocSponsor = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/sponsor')
          .success(function (data) {
            $scope.sponsor = angular.copy({id: data.id, text: data.fname + " " + data.lname + " - " + data.email});
          }).error(function (data) {
            console.error("Error getting document sponsor: %o", data);
          });
      };

      $scope.getDocStatus = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/status')
          .success(function (data) {
            $scope.status = angular.copy({id: data.id, text: data.label});
          }).error(function (data) {
            console.error("Error getting document status: %o", data);
          });
      };

      $scope.getAllStatuses = function () {
        $http.get('/api/docs/statuses')
          .success(function (data) {
            angular.forEach(data, function (status) {
              $scope.suggestedStatuses.push(status.label);
            });
          }).error(function (data) {
            console.error("Unable to get document statuses: %o", data);
          });
      };

      $scope.getAllCategories = function () {
        return $http.get('/api/docs/categories')
          .success(function (data) {
            angular.forEach(data, function (category) {
              $scope.suggestedCategories.push(category.name);
            });
          })
          .error(function (data) {
            console.error("Unable to get document categories: %o", data);
          });
      };

      $scope.saveStatus = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/status', {status: $scope.status})
          .success(function (data) {
            console.log("Status saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving status: %o", data);
          });
      };

      $scope.saveSponsor = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/sponsor', {'sponsor': $scope.sponsor})
          .success(function (data) {
            console.log("Sponsor saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving sponsor: %o", data);
          });
      };

      $scope.saveCategories = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/categories', {'categories': $scope.categories})
          .success(function (data) {
            console.log("Categories saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving categories for document %o: %o \n %o", $scope.doc, $scope.categories, data);
          });
      };
    }
    ]);