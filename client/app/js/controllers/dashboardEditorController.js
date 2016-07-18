/*global Markdown*/
/*global alert*/
angular.module('madisonApp.controllers')
  .controller('DashboardEditorController', ['$scope', '$http', '$timeout', '$q',
      '$location', '$filter', 'growl', '$upload', 'modalService', 'Doc',
      '$translate', 'pageService', '$state', 'SITE',
    function ($scope, $http, $timeout, $q, $location, $filter, growl, $upload,
      modalService, Doc, $translate, pageService, $state, SITE) {

      $translate('content.editdocument.title', {title: SITE.name})
      .then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.doc = {};
      $scope.docContent = '';

      $scope.stats = {};

      $scope.newdate = {
        label: '',
        date: new Date()
      };

      $scope.availableCategories = [];
      $scope.availableSponsors = [];
      $scope.availableStatuses = [];
      $scope.dates = [];
      $scope.publishStates = [
        'unpublished',
        'published',
        'private'
      ];
      $scope.discussionStates = [
        'open',
        'closed',
        'hidden'
      ];

      $scope.saveStatus = 'saved';
      $scope.saveTimeout = null;

      $scope.featuredImage = null;
      $scope.featuredDoc = false;

      $scope.currentPage = 1;

      $scope.contentLoaded = false;
      $scope.markdownEditor = null;

      // Size of MEDIUMTEXT field in MySQL
      $scope.contentMax = 16777215;
      // Good, safe size.
      $scope.contentWarning = 100000;

      var abs = $location.absUrl();
      var id = abs.match(/.*\/(\d+)$/)[1];

      $scope.getDoc = function (id) {
        return $q(function(resolve, reject) {
          $http.get('/api/docs/' + id)
            .success(function (data) {
              $scope.doc = data;

              getContent(1, function() {
                $scope.contentLoaded = true;
                resolve();
              });

              processDates(data.dates);
              processThumbnail(data.thumbnail);

              $translate('content.editdocument.title.loaded', {
                title: SITE.name,
                docTitle: data.title
              })
              .then(function(translation) {
                pageService.setTitle(translation);
              });

            });
          });
      };

      /**
       * Sponsors
       * ----------------------------------------------
       */

      $scope.getAllSponsors = function() {
        $http.get('/api/user/sponsors/all')
        .success(function(data) {
          $scope.availableSponsors = data.sponsors;
        }).error(function() {
          console.error('Unable to get available sponsors:', data);
        });
      };

      /**
       * Statuses
       * ----------------------------------------------
       */

      $scope.getAllStatuses = function () {
        $http.get('/api/docs/statuses')
        .success(function (data) {
          angular.forEach(data, function (status) {
            $scope.availableStatuses.push(status.label);
          });
        }).error(function (data) {
          console.error("Unable to get document statuses: %o", data);
        });
      };

      /**
       * Categories
       * ----------------------------------------------
       */

      $scope.getAllCategories = function () {
        return $http.get('/api/docs/categories')
          .success(function (data) {
            $scope.availableCategories = data;
          })
          .error(function (data) {
            console.error("Unable to get document categories: %o", data);
          });
      };

      /**
       * Needed for ui-select on categories.
       *
       * Takes selected category from the ui-select and transforms it into the
       * proper category object to add to selected categories array.
       */
      $scope.categoryTransform = function(category) {
        return { name: category };
      };

      /**
       * Featured
       * ----------------------------------------------
       */

      $scope.tryFeaturedDoc = function () {
        $scope._setFeaturedDoc();
      };

      $scope._setFeaturedDoc = function () {
        $http.post('/api/docs/featured', {id: $scope.doc.id}).then(function(data) {
          checkFeatured(data.data);
        });
      };

      $scope.removeFeaturedDoc = function() {
        $http.delete('/api/docs/featured/' + $scope.doc.id).then(function(data) {
          checkFeatured(data.data);
        });
      };

      function checkFeatured(data) {
        for(var i in data) {
          if($scope.doc.id == data[i].id) {
            $scope.doc.featured = true;
            return;
          }
        }

        $scope.doc.featured = false;
      }

      /**
       * Statistics
       * ----------------------------------------------
       */
      $scope.getStats = function() {
        return Doc.getActivity({
            id: id
        }).$promise.then(function(data) {
            $scope.stats = data;
        });
      }

      /**
       * Pages
       * ----------------------------------------------
       */

      $scope.loadPage = function(page) {
        // First, save our current work.
        $scope.saveContent( function() {
          // Next, set us in loading mode.
          $scope.contentLoaded = false;
          // Then get the content.
          getContent(page, function() {
            // It takes just a moment for the content to load and be ready.
            // We don't have an event for when that's done, so just wait a
            // really short time.
            setTimeout(function() {
              // Done loading.
              $scope.contentLoaded = true;
              $scope.markdownEditor.refreshPreview();
              $scope.saveStatus = 'saved';
            }, 100);
          });
        });
      };

      $scope.addPage = function() {
        $scope.contentLoaded = false;
        return $http.post('/api/docs/' + $scope.doc.id + '/content', {})
          .success(function (data) {
            // Re-set our max page count.
            $scope.doc.pages = data.page;
            // Load our new page.
            $scope.loadPage(data.page);

            console.log("Content saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving content for document:", data);
          });
      };

      $scope.deletePage = function(pageNum) {
        $scope.contentLoaded = false;
        if(!pageNum) {
          pageNum = $scope.currentPage;
        }
        return $http.delete('/api/docs/' + $scope.doc.id + '/content/' + pageNum)
          .success(function (data) {
            $scope.doc.pages = data.pages;

            var nextPage = pageNum - 1;
            if(nextPage < 1) {
              nextPage = 1;
            }
            $scope.loadPage(nextPage);
          })
          .error(function (data) {
            console.error("Unable to delete page: %o", data);
          });
      };

      $scope.range = (function() {
        var cache = {};
        return function(min, max, step) {
          var isCacheUseful = (max - min) > 70;
          var cacheKey;

          if(!min) {
            min = 0;
          }
          if(!step) {
            step = 1;
          }

          if (isCacheUseful) {
            cacheKey = max + ',' + min + ',' + step;

            if (cache[cacheKey]) {
              return cache[cacheKey];
            }
          }

          var _range = [];
          step = step || 1;
          for (var i = min; i <= max; i += step) {
            _range.push(i);
          }

          if (isCacheUseful) {
            cache[cacheKey] = _range;
          }

          return _range;
        };
      })();

      /**
       * Files / Images
       * ----------------------------------------------
       */

      $scope.$watch('files', function (newValue, oldValue) {
        if (newValue !== oldValue) {
          $scope.uploadImage($scope.files);
        }
      });

      $scope.deleteFeaturedImage = function () {
        if ($scope.doc.featured === true) {
          growl.error($translate.instant('errors.document.edit.imagerequired'));
        } else {
          return $http.delete('/api/docs/' + $scope.doc.id + '/featured-image')
            .success(function () {
              $scope.featuredImage = null;
              $scope.doc.thumbnail = null;
            }).error(function(data, status) {
              if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            });
        }
      };

      $scope.uploadImage = function (file) {
        //This is passed an empty array when the file selection is shown for
        //some reason (?)
        if (file.length === 0) {
          return;
        }

        $scope.uploadProgress = 0;
        $scope.uploadType = 'info';

        if (file && file.length === 1) {
          $upload.upload({
            url: '/api/docs/' + $scope.doc.id + '/featured-image',
            file: file
          })
            //Update the progress bar
            .progress(function (event) {
              var progressPercentage =
                parseInt(100.0 * event.loaded / event.total, 10);

              $scope.uploadProgress = progressPercentage;
            })
            //Update progress bar class on success
            .success(function (data, status, headers, config) {
              $scope.uploadType = 'success';

              $scope.featuredImage = {path: data.imagePath + '?' +
                new Date().getTime()};
            })
            //Update progress bar class on error
            .error(function () {
              $scope.uploadType = 'danger';
            })
            //Remove progress bar
            .finally(function () {
              $timeout(function () {
                $scope.uploadProgress = null;
              }, 5000);
            });
        } else {
          console.error("Error uploading %o", file);
          growl.error($translate.instant('errors.document.edit.image'));
        }
      };

      /**
       * Save / Delete
       * ----------------------------------------------
       */

      $scope.saveDocument = function () {
        return $http.put('/api/docs/' + $scope.doc.id, $scope.doc)
          .success(function(data) {
            angular.merge($scope.doc, data);
            //growl.success($translate.instant('success.general.save'));
            $scope.saveStatus = 'saved';
          }).error(function(data) {
            if (status === 403) {
              growl.error($translate.instant('errors.general.unauthorized'));
            } else {
              growl.error($translate.instant('errors.general.save'));
            }

            $scope.saveStatus = 'unsaved';
            console.error("Error saving publish state for document:", data);
          });
      };

      $scope.saveContent = function (callback) {
        if ($scope.contentLoaded) {
          return $http.put(
            '/api/docs/' + $scope.doc.id + '/content/' + $scope.currentPage,
            {'content': $scope.docContent}
          ).success(function (data) {
            $scope.saveStatus = 'saved';
            if (callback) callback();
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving content for document:", data);
            $scope.saveStatus = 'unsaved';
            if (callback) callback();
          });
        } else {
          console.log('Not saving until content is loaded.');
          if (callback) callback();
        }
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
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
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
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Unable to save date: %o (%o)", date, data);
          });
      };

      $scope.deleteDocument = function(asAdmin) {
        var modalBody;
        var deleteUrl = '/api/docs/' + $scope.doc.id;

        if (asAdmin) {
          modalBody = 'form.document.delete.admin.confirm.body';
          deleteUrl += '?admin=true';
        } else {
          modalBody = 'form.document.delete.confirm.body';
        }

        var modalOptions = {
          closeButtonText:
            $translate.instant('form.general.cancel'),
          actionButtonText:
            $translate.instant('form.document.delete'),
          headerText:
            $translate.instant('form.document.delete.confirm'),
          bodyText:
            $translate.instant(modalBody)
        };

        modalService.showModal({}, modalOptions)
        .then(function() {
          $http.delete(deleteUrl)
          .success(function() {
            growl.success($translate.instant('form.document.delete.success'));
            if (asAdmin) {
              $state.go('dashboard-docs-list');
            } else {
              $state.go('my-documents');
            }
          }).error(function() {
            growl.error($translate.instant('errors.document.delete'));
          });
        });
      };

      /**
       * Utility
       * ----------------------------------------------
       */

      function clean_slug(string) {
        return string.toLowerCase().replace(/[^a-zA-Z0-9\- ]/g, '').
          replace(/ +/g, '-');
      }

      function getContent(page, callback) {
        return Doc.getDocContent({
          id: $scope.doc.id, format: 'raw', page: page
        }).$promise.then(function(data) {
          $scope.docContent = data.raw;
          $scope.currentPage = page;
          if(callback) {
            callback();
          }
        });
      }

      function processDates(dates) {
        angular.forEach(dates, function (date, index) {
          date.date = Date.parse(date.date);
          date.$changed = false;
          $scope.dates.push(angular.copy(date));

          $scope.$watch('dates[' + index + ']', function (newitem, olditem) {
            if (!angular.equals(newitem, olditem) && newitem !== undefined) {
              newitem.$changed = true;
            }
          }, true);
        });
      }

      function processThumbnail(thumbnail) {
        if (thumbnail !== null) {
          $scope.featuredImage = {
            path: thumbnail + '?' + new Date().getTime()
          };
        }
      }

      function debounceSaveUpdates(newObject, oldObject, saveFunc) {
        if (newObject !== oldObject) {
          $scope.saveStatus = 'saving';

          if ($scope.saveTimeout) {
            $timeout.cancel($scope.saveTimeout);
          }

          $scope.saveTimeout = $timeout(saveFunc, 1000, true);
        }
      }

      /**
       * Start actual execution
       */

      $scope.getAllSponsors();
      $scope.getAllCategories();
      $scope.getStats();
      $scope.getDoc(id).then(function () {

        $scope.markdownEditor = new Markdown.Editor(
          Markdown.getSanitizingConverter()
        );

        $scope.markdownEditor.run();

        // We don't control the pagedown CSS, and this DIV needs to be
        // scrollable
        $("#wmd-preview").css("overflow", "scroll");

        // Resizing dynamically according to the textarea is hard,
        // so just set the height once (22 is padding)
        $("#wmd-preview").css("height", ($("#wmd-input").height() + 22));
        $("#wmd-input").scroll(function () {
          $("#wmd-preview").scrollTop($("#wmd-input").scrollTop());
        });

        // TODO: How can this be handled on the doc object directly?
        $scope.$watch('doc.slug', function(newSlug, oldSlug) {
          if (newSlug === oldSlug) return;

          // Changing doc.slug in-place will trigger the $watch
          var safe_slug = $scope.doc.slug;
          var sanitized_slug = clean_slug(safe_slug);
          // If cleaning the slug didn't change anything, we have a valid NEW
          // slug, and we can save it
          if (safe_slug === sanitized_slug) {
            debounceSaveUpdates(newSlug, oldSlug, $scope.saveDocument);
          } else {
            // Change the slug in-place, which will trigger another watch
            // (handled by the POST function)
            growl.error($translate.instant('errors.document.edit.slug'));
            $scope.doc.slug = sanitized_slug;
          }
        });

        $scope.$watch('doc', function(newDoc, oldDoc) {
          debounceSaveUpdates(newDoc, oldDoc, $scope.saveDocument);
        }, true);

        $scope.$watch('docContent', function(newContent, oldContent) {
          debounceSaveUpdates(newContent, oldContent, $scope.saveContent);
        });
      });

    }
  ]);
