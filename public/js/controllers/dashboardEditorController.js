/*global Markdown*/
/*global alert*/
angular.module('madisonApp.controllers')
  .controller('DashboardEditorController', ['$scope', '$http', '$timeout',
      '$location', '$filter', 'growl', '$upload', 'modalService', 'Doc',
      '$translate', 'pageService', 'SITE',
    function ($scope, $http, $timeout, $location, $filter, growl, $upload,
      modalService, Doc, $translate, pageService, SITE) {

      pageService.setTitle($translate.instant('content.editdocument.title',
        {title: SITE.name}));

      $scope.doc = {};
      $scope.sponsor = {};
      $scope.status = {};
      $scope.newdate = {
        label: '',
        date: new Date()
      };
      $scope.verifiedUsers = [];
      $scope.categories = [];
      $scope.introtext = "";
      $scope.suggestedCategories = [];
      $scope.publishStates = [
        'unpublished',
        'published',
        'private'
      ];
      $scope.suggestedStatuses = [];
      $scope.dates = [];
      $scope.featuredImage = null;
      $scope.featuredDoc = false;

      var abs = $location.absUrl();
      var id = abs.match(/.*\/(\d+)$/)[1];

      $scope.$watch('files', function (newValue, oldValue) {
        if (newValue !== oldValue) {
          $scope.uploadImage($scope.files);
        }
      });

      function clean_slug(string) {
        return string.toLowerCase().replace(/[^a-zA-Z0-9\- ]/g, '').
          replace(/ +/g, '-');
      }

      $scope.getDoc = function (id) {
        return $http.get('/api/docs/' + id)
          .success(function (data) {
            $scope.doc = data;

            angular.forEach(data.categories, function (category) {
              $scope.categories.push(angular.copy(category.name));
            });

            if ($scope.doc.thumbnail !== null) {
              $scope.featuredImage = {path: $scope.doc.thumbnail + '?' +
                new Date().getTime()};
            }

            pageService.setTitle($translate.instant(
              'content.editdocument.title.loaded',
              {title: SITE.name, docTitle: data.title}));
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

      $scope.setSelectOptions = function () {
        $scope.categoryOptions = {
          placeholder: $translate.instant('form.document.category.placeholder'),
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
          placeholder: $translate.instant('form.document.status.placeholder'),
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
          placeholder: $translate.instant('form.document.sponsor.placeholder'),
          allowClear: true,
          ajax: {
            url: "/api/user/sponsors/all",
            dataType: 'json',
            data: function () {
              return;
            },
            results: function (data) {
              var returned = [];

              if (!data.success) {
                alert(data.message);
                return;
              }

              angular.forEach(data.sponsors, function (sponsor) {
                var text = "";

                switch (sponsor.sponsorType) {
                case 'group':
                  text = $translate.instant('form.document.sponsor.groupflag',
                    {name: sponsor.name});
                  break;
                case 'user':
                  text = sponsor.fname + " " + sponsor.lname + " - " +
                    sponsor.email;
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

      /**
      * Start actual execution
      */

      var docDone = $scope.getDoc(id);

      $scope.getAllCategories();
      $scope.getVerifiedUsers();
      $scope.setSelectOptions();

      var initCategories = true;
      var initSponsor = true;
      var initStatus = true;

      var initTitle = true;
      var initPrivate = true;
      var initSlug = true;
      var initContent = true;

      docDone.then(function () {
        new Markdown.Editor(Markdown.getSanitizingConverter()).run();

        // We don't control the pagedown CSS, and this DIV needs to be
        // scrollable
        $("#wmd-preview").css("overflow", "scroll");

        // Resizing dynamically according to the textarea is hard,
        // so just set the height once (22 is padding)
        $("#wmd-preview").css("height", ($("#wmd-input").height() + 22));
        $("#wmd-input").scroll(function () {
          $("#wmd-preview").scrollTop($("#wmd-input").scrollTop());
        });

        //Save intro text after a 3 second timeout
        var introTextTimeout = null;
        $scope.updateIntroText = function (newValue) {
          if (introTextTimeout) {
            $timeout.cancel(introTextTimeout);
          }
          introTextTimeout = $timeout(function () {
              $scope.saveIntroText(newValue);
          }, 3000);
        };

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

        $scope.getIntroText();

        $scope.getDocDates();

        $scope.$watch('doc.title', function () {
          if (initTitle) {
            $timeout(function () {
              initTitle = false;
            });
          } else {
            $scope.saveTitle();
          }
        });

        $scope.$watch('doc.slug', function () {
          if (initSlug) {
            $timeout(function () {
              initSlug = false;
            });
          } else {
            // Changing doc.slug in-place will trigger the $watch
            var safe_slug = $scope.doc.slug;
            var sanitized_slug = clean_slug(safe_slug);
            // If cleaning the slug didn't change anything, we have a valid NEW
            // slug, and we can save it
            if (safe_slug === sanitized_slug) {
              $scope.saveSlug();
            } else {
              // Change the slug in-place, which will trigger another watch
              // (handled by the POST function)
              growl.error($translate.instant('errors.document.edit.slug'));
              console.log('Invalid slug, reverting');
              $scope.doc.slug = sanitized_slug;
            }
          }
        });

        $scope.$watch('doc.publish_state', function () {
          if (initPrivate) {
            $timeout(function () {
              initPrivate = false;
            });
          } else {
            $scope.savePublishState();
          }
        });

        // Save the content every 5 seconds
        var timeout = null;
        $scope.$watch('doc.content.content', function () {
          if (initContent) {
            $timeout(function () {
              initContent = false;
            });
          } else {
            if (timeout) {
              $timeout.cancel(timeout);
            }
            timeout = $timeout(function () { $scope.saveContent(); }, 5000);
          }
        });
      });

      /**
      * getShortUrl
      *
      * Makes API call to opngv.us/api
      *   Runs when the 'Get Short Url' button is clicked on the 'Document
      *   Information' tab.
      */
      $scope.getShortUrl = function () {
        /**
        * Hardcoded API Credentials
        */
        // TODO: Why are these hardcoded?
        var opngv = {
          username: 'madison-robot',
          password: 'MeV3MJJE',
          api: 'http://opngv.us/yourls-api.php'
        };

        //Construct document url
        var slug = $scope.doc.slug;
        var long_url = $location.protocol() + '://' + $location.host() +
          '/docs/' + slug;

        $http({
          url: opngv.api,
          method: 'JSONP',
          params: {
            callback: 'JSON_CALLBACK',
            action: 'shorturl',
            format: 'jsonp',
            url: long_url,
            username: opngv.username,
            password: opngv.password
          }
        }).success(function (data) {
          $scope.short_url = data.shorturl;
        }).error(function (data) {
          console.error("Error generating short url: %o", data);
          growl.error($translate.instant('errors.document.edit.shorturl'));
        });
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

      $scope.saveTitle = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/title',
            {'title': $scope.doc.title})
          .success(function (data) {
            console.log("Title saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving title for document:", data);
          });
      };

      $scope.savePublishState = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/publishstate',
            {'publish_state': $scope.doc.publish_state})
          .success(function (data) {
            console.log("Publish state saved successfully: %o", data);
          }).error(function (data) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving publish state for document:", data);
          });
      };

      $scope.saveSlug = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/slug',
            {'slug': $scope.doc.slug})
          .success(function (data) {
            console.log("Slug sent: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving slug for document:", data);
          });
      };

      $scope.saveContent = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/content',
            {'content': $scope.doc.content.content})
          .success(function (data) {
            console.log("Content saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving content for document:", data);
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

      $scope.getDocCategories = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/categories')
          .success(function (data) {
            angular.forEach(data, function (category) {
              $scope.categories.push(category.name);
            });
          }).error(function (data) {
            console.error("Unable to get categories for document %o: %o",
              $scope.doc, data);
          });
      };

      $scope.getIntroText = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/introtext')
          .success(function (data) {
            $scope.introtext = data.meta_value;
          }).error(function (data) {
            console.error("Unable to get Intro Text for document %o: %o",
              $scope.doc, data);
          });
      };

      $scope.getDocSponsor = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/sponsor')
          .success(function (data) {
            if (data.sponsorType === undefined) {
              $scope.sponsor = null;
              return;
            }

            var text = "";

            switch (data.sponsorType.toLowerCase()) {
            case 'group':
              text = $translate.instant('form.document.sponsor.groupflag',
                {name: data.name});
              break;
            case 'user':
              // TODO: i18n on names is hard. Revisit this.
              text = data.fname + " " + data.lname + " - " + data.email;
              break;
            }

            $scope.sponsor = {
              id : data.id,
              type :  data.sponsorType.toLowerCase(),
              text : text
            };
          }).error(function (data) {
            console.error("Error getting document sponsor: %o", data);
          });
      };

      $scope.getDocStatus = function () {
        return $http.get('/api/docs/' + $scope.doc.id + '/status')
          .success(function (data) {
            if (data.id === undefined) {
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

      $scope.saveStatus = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/status', {
          status: $scope.status
        })
          .success(function (data) {
            console.log("Status saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving status: %o", data);
          });
      };

      $scope.saveSponsor = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/sponsor', {
          'sponsor': $scope.sponsor
        })
          .success(function (data) {
            console.log("Sponsor saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving sponsor: %o", data);
          });
      };

      $scope.saveCategories = function () {
        return $http.post('/api/docs/' + $scope.doc.id + '/categories', {
          'categories': $scope.categories
        })
          .success(function (data) {
            console.log("Categories saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving categories for document %o: %o \n %o",
              $scope.doc, $scope.categories, data);
          });
      };

      //Triggered 5 seconds after last change to textarea with ng-model="introtext"
      $scope.saveIntroText = function (introtext) {
        return $http.post('/api/docs/' + $scope.doc.id + '/introtext', {
          'intro-text': introtext
        })
          .success(function (data) {
            console.log("Intro Text saved successfully: %o", data);
          }).error(function (data, status) {
            if (status === 403) growl.error($translate.instant('errors.general.unauthorized'));
            console.error("Error saving intro text for document %o: %o",
              $scope.doc, $scope.introtext);
          });
      };

      //Handle image uploads
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

      $scope.tryFeaturedDoc = function () {
        $scope._setFeaturedDoc();
        /*
        //Check if any other documents are featured
        var featuredDoc = Doc.getFeaturedDoc();

        //Wait for the response
        featuredDoc.$promise.then(function () {
          //If so, display confirmation
          if (featuredDoc.length) {
            var bodyText;

            if (featuredDoc.id === $scope.doc.id) {
              bodyText = $translate.instant('errors.document.edit.unfeatured');
            } else {
              bodyText = $translate.instant('errors.document.edit.multiplefeatured');
            }


            var modalOptions = {
              closeButtonText:
                $translate.instant('form.general.cancel'),
              actionButtonText:
                $translate.instant('form.document.featured.change'),
              headerText:
                $translate.instant('form.document.featured.confirm'),
              bodyText: bodyText
            };

            //Open the dialog
            var res = modalService.showModal({}, modalOptions);

            //Reset doc featured status on cancel
            res.catch(function () {
              $scope.doc.featured = !$scope.doc.featured;
            });

            //Only executed if the user confirms.  Set this doc as featured.
            res.then(function () {
              $scope._setFeaturedDoc();
            });
          } else {
            //If the featured document isn't set, set this one as the featured
            //document
            $scope._setFeaturedDoc();
          }
        });
        */
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
        console.log(data);
        for(var i in data) {
          if($scope.doc.id == data[i].id) {
            $scope.doc.featured = true;
            return;
          }
        }

        $scope.doc.featured = false;
      }
    }
    ]);
