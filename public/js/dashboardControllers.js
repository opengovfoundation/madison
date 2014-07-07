/*global Markdown*/
/*global clean_slug*/
angular.module('madisonApp.dashboardControllers', [])
  .controller('DashboardVerifyGroupController', ['$scope', '$http', function($scope, $http) {
    $scope.requests = [];

    $scope.init = function(){
        $scope.getRequests();
    };

    $scope.getRequests = function(){
        $http.get('/api/groups/verify')
        .success(function(data, status, headers, config){
            $scope.requests = data;
        })
        .error(function(data, status, headers, config){
            console.error(data);
        });
    };

    $scope.update = function(request, status, event){
        $http.post('/api/groups/verify', {'request': request, 'status': status})
        .success(function(data){
            request.status = status;
        })
        .error(function(data, status, headers, config){
            console.error(data);
        });
    };
  }])
  .controller('DashboardVerifyUserController', ['$scope', '$http', function($scope, $http) {
    $scope.requests = [];

    $scope.init = function(){
        $scope.getRequests();
    };

    $scope.getRequests = function(){
        $http.get('/api/user/independent/verify')
        .success(function(data, status, headers, config){
            $scope.requests = data;
        })
        .error(function(data, status, headers, config){
            console.error(data);
        });
    };

    $scope.update = function(request, status, event){
        $http.post('/api/user/independent/verify', {'request': request, 'status': status})
        .success(function(data){
            request.meta_value = status;
            location.reload();
        })
        .error(function(data, status, headers, config){
            console.error(data);
        });
    };
  }])
  .controller('DashboardDocumentsController', ['$scope', '$http', '$filter',
    function ($scope, $http, $filter) {
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
    ])
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
        $http.post('/api/user/verify', {
          'request': request,
          'status': status
        })
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

        $http.post('/api/user/admin', {
          'admin': admin
        })
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
      $scope.newdate = {
        label: '',
        date: new Date()
      };
      $scope.verifiedUsers = [];
      $scope.categories = [];
      $scope.suggestedCategories = [];
      $scope.suggestedStatuses = [];
      $scope.dates = [];

      $scope.init = function () {
        var abs = $location.absUrl();
        var id = abs.match(/.*\/(\d+)$/)[1];

        function clean_slug(string) {
          return string.toLowerCase().replace(/[^a-zA-Z0-9\- ]/g, '').replace(/ +/g, '-');
        }

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
                $timeout(function () {
                  initSponsor = false;
                });
              } else {
                $scope.saveSponsor();
              }
            });
          });

          $scope.getDocStatus().then(function () {
            $scope.$watch('status', function () {
              if (initStatus) {
                $timeout(function () {
                  initStatus = false;
                });
              } else {
                $scope.saveStatus();
              }
            });
          });

          $scope.getDocCategories().then(function () {
            $scope.$watch('categories', function () {
              if (initCategories) {
                $timeout(function () {
                  initCategories = false;
                });
              } else {
                $scope.saveCategories();
              }
            });
          });

          $scope.getDocDates();

          $scope.$watchCollection('[doc.slug, doc.title, doc.content.content]', function () {
            if (initDoc) {
              $timeout(function () {
                initDoc = false;
              });
            } else {
              $scope.doc.slug = clean_slug($scope.doc.slug);
              $scope.saveDoc();
            }
          });
        });
      };

      $scope.setSelectOptions = function () {
        $scope.categoryOptions = {
          placeholder: "Add document categories",
          multiple: true,
          simple_tags: true,
          tokenSeparators: [","],
          tags: function () {
            return $scope.suggestedCategories;
          },
          results: function () {
            return $scope.categories;
          },
          initSelection: function (element, callback) {
            var returned = [];
            angular.forEach($scope.categories, function (category, index) {
              returned.push(angular.copy({id: index, text: category}));
            });

            callback(returned);
          }
        };

        /*jslint unparam: true*/
        $scope.statusOptions = {
          placeholder: "Select Document Status",
          ajax: {
            url: "/api/docs/statuses",
            dataType: 'json',
            data: function (term, page) {
              return;
            },
            results: function (data, page) {
              var returned = [];
              angular.forEach(data, function (status) {
                returned.push({
                  id: status.id,
                  text: status.label
                });
              });
              return {
                results: returned
              };
            }
          },
          data: function () {
            return $scope.suggestedStatuses;
          },
          results: function () {
            return $scope.status;
          },
          createSearchChoice: function (term) {
            return {
              id: term,
              text: term
            };
          },
          initSelection: function (element, callback) {
            callback($scope.status);
          },
          allowClear: true
        };

        $scope.sponsorOptions = {
          placeholder: "Select Document Sponsor",
          allowClear: true,
          ajax: {
            url: "/api/user/sponsors/all",
            dataType: 'json',
            data: function () {
              return;
            },
            results: function (data) {
              var returned = [];
              
              if(!data.success) {
                  alert(data.message);
                  return;
              }
              
              angular.forEach(data.sponsors, function (sponsor) {
                var text = "";
                
                switch(sponsor.sponsorType) {
                    case 'group':
                        text = "[Group] " + sponsor.name;
                        break;
                    case 'user':
                        text = sponsor.fname + " " + sponsor.lname + " - " + sponsor.email;
                        break;
                } 
                
                returned.push({ 
                    id : sponsor.id,
                    type :  sponsor.sponsorType,
                    text : text
                }); 
                
              });

              return {
                results: returned
              };
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

            angular.forEach(data.categories, function (category) {
              $scope.categories.push(angular.copy(category.name));
            });
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

          $http.post('/api/docs/' + $scope.doc.id + '/dates', {
            date: $scope.newdate
          })
            .success(function (data) {
              data.date = Date.parse(data.date);
              data.$changed = false;
              $scope.dates.push(data);

              $scope.newdate = {
                label: '',
                date: new Date()
              };
            }).error(function (data) {
              console.error("Unable to save date: %o", data);
            });
        }
      };

      $scope.deleteDate = function (date) {
        $http['delete']('/api/docs/' + $scope.doc.id + '/dates/' + date.id)
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

        return $http.put('/api/dates/' + date.id, {
          date: sendDate
        })
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
            $scope.sponsor = {
              id: data.id,
              text: data.fname + " " + data.lname + " - " + data.email
            };
          }).error(function (data) {
            console.error("Error getting document sponsor: %o", data);
          });
      };

      $scope.getDocStatus = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/status')
          .success(function (data) {
            if (data.id === 'undefined') {
              $scope.status = null;
            } else {
              $scope.status = {
                id: data.id,
                text: data.label
              };
            }
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
        return $http.post('/api/docs/' + $scope.doc.id + '/status', {
          status: $scope.status
        })
          .success(function (data) {
            console.log("Status saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving status: %o", data);
          });
      };

      $scope.saveSponsor = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/sponsor', {
          'sponsor': $scope.sponsor
        })
          .success(function (data) {
            console.log("Sponsor saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving sponsor: %o", data);
          });
      };

      $scope.saveCategories = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/categories', {
          'categories': $scope.categories
        })
          .success(function (data) {
            console.log("Categories saved successfully: %o", data);
          }).error(function (data) {
            console.error("Error saving categories for document %o: %o \n %o", $scope.doc, $scope.categories, data);
          });
      };
    }
    ]);
