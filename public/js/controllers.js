/*global user*/
/*global doc*/
angular.module('madisonApp.controllers', [])
  .controller('HomePageController', ['$scope', '$http', '$filter',
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

      $scope.init = function () {
        $scope.getDocs();

        $scope.select2Config = {
          multiple: true,
          allowClear: true,
          placeholder: "Filter documents by category, sponsor, or status"
        };

        $scope.dateSortConfig = {
          allowClear: true,
          placeholder: "Sort By Date"
        };
      };

      $scope.docFilter = function (doc) {

        var show = false;

        if ($scope.select2 !== undefined && $scope.select2 !== '') {
          var cont = true;

          angular.forEach(doc.categories, function (category) {
            if (category.name === $scope.select2 && cont) {
              show = true;
              cont = false;
            }
          });

          angular.forEach(doc.sponsor, function (sponsor) {
            if (sponsor.id === $scope.select2 && cont) {
              show = true;
              cont = false;
            }
          });

          angular.forEach(doc.statuses, function (status) {
            if (status.id === $scope.select2 && cont) {
              show = true;
              cont = false;
            }
          });

        } else {
          show = true;
        }

        return show;
      };

      $scope.getDocs = function () {
        $http.get('/api/docs')
          .success(function (data) {

            angular.forEach(data, function (doc) {
              doc.updated_at = Date.parse(doc.updated_at);
              doc.created_at = Date.parse(doc.created_at);

              $scope.docs.push(doc);

              angular.forEach(doc.categories, function (category) {
                var found = $filter('filter')($scope.categories, category, true);

                if (!found.length) {
                  $scope.categories.push(category.name);
                }
              });

              angular.forEach(doc.sponsor, function (sponsor) {
                var found = $filter('filter')($scope.sponsors, sponsor, true);

                if (!found.length) {
                  $scope.sponsors.push(sponsor);
                }
              });

              angular.forEach(doc.statuses, function (status) {
                var found = $filter('filter')($scope.statuses, status, true);

                if (!found.length) {
                  $scope.statuses.push(status);
                }
              });

              angular.forEach(doc.dates, function (date) {
                date.date = Date.parse(date.date);
              });
            });

          })
          .error(function (data) {
            console.error("Unable to get documents: %o", data);
          });
      };
    }
    ])
  .controller('DocumentPageController', ['$scope', '$cookies',
    function ($scope, $cookies) {
      $scope.hideIntro = $cookies.hideIntro;

      $scope.hideHowToAnnotate = function () {
        $cookies.hideIntro = true;
        $scope.hideIntro = true;
      };
    }
    ])
  .controller('ReaderController', ['$scope', '$http', 'annotationService',
    function ($scope, $http, annotationService) {
      $scope.annotations = [];

      $scope.$on('annotationsUpdated', function () {
        $scope.annotations = annotationService.annotations;
        $scope.$apply();
      });

      $scope.init = function () {
        $scope.user = user;
        $scope.doc = doc;

        $scope.getSupported();
      };

      $scope.getSupported = function () {
        if ($scope.user.id !== '') {
          $http.get('/api/users/' + $scope.user.id + '/support/' + $scope.doc.id)
            .success(function (data) {
              switch (data.meta_value) {
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
            }).error(function () {
              console.error("Unable to get support info for user %o and doc %o", $scope.user, $scope.doc);
            });
        }
      };

      $scope.support = function (supported) {
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
          })
          .error(function (data) {
            console.error("Error posting support: %o", data);
          });
      };
    }
    ])
  .controller('ParticipateController', ['$scope', '$http', 'annotationService', 'createLoginPopup',
    function ($scope, $http, annotationService, createLoginPopup) {
      $scope.annotations = [];
      $scope.comments = [];
      $scope.activities = [];
      $scope.supported = null;
      $scope.opposed = false;

      $scope.init = function (docId) {
        $scope.getDocComments(docId);
        $scope.user = user;
        $scope.doc = doc;
      };

      $scope.$on('annotationsUpdated', function () {
        angular.forEach(annotationService.annotations, function (annotation) {
          if ($.inArray(annotation, $scope.activities) < 0) {
            annotation.label = 'annotation';
            annotation.commentsCollapsed = true;
            $scope.activities.push(annotation);
          }

        });

        $scope.$apply();
      });

      $scope.getDocComments = function (docId) {
        $http({
          method: 'GET',
          url: '/api/docs/' + docId + '/comments'
        })
          .success(function (data) {
            angular.forEach(data, function (comment) {
              comment.label = 'comment';
              comment.commentsCollapsed = true;
              $scope.activities.push(comment);
            });
          })
          .error(function (data) {
            console.error("Error loading comments: %o", data);
          });
      };

      $scope.commentSubmit = function () {

        var comment = angular.copy($scope.comment);
        comment.user = $scope.user;
        comment.doc = $scope.doc;

        $http.post('/api/docs/' + comment.doc.id + '/comments', {
          'comment': comment
        })
          .success(function () {
            comment.label = 'comment';
            comment.user.fname = comment.user.name;
            $scope.activities.push(comment);
            $scope.comment.text = '';
          })
          .error(function (data) {
            console.error("Error posting comment: %o", data);
          });
      };

      $scope.activityOrder = function (activity) {
        var popularity = activity.likes - activity.dislikes;

        return popularity;
      };

      $scope.addAction = function (activity, action, $event) {
        if ($scope.user.id !== '') {
          $http.post('/api/docs/' + $scope.doc.id + '/' + activity.label + 's/' + activity.id + '/' + action)
            .success(function (data) {
              activity.likes = data.likes;
              activity.dislikes = data.dislikes;
              activity.flags = data.flags;
            }).error(function (data) {
              console.error(data);
            });
        } else {
          createLoginPopup($event);
        }

      };

      $scope.collapseComments = function (activity) {
        activity.commentsCollapsed = !activity.commentsCollapsed;
      };

      $scope.subcommentSubmit = function (activity, subcomment) {
        subcomment.user = $scope.user;

        $.post('/api/docs/' + $scope.doc.id + '/' + activity.label + 's/' + activity.id + '/comments', {
          'comment': subcomment
        })
          .success(function (data) {
            activity.comments.push(data);
            subcomment.text = '';
            subcomment.user = '';
            $scope.$apply();
          }).error(function (data) {
            console.error(data);
          });
      };
    }
    ])
  .controller('UserPageController', ['$scope', '$http', '$location',
    function ($scope, $http, $location) {
      $scope.user = {};
      $scope.meta = '';
      $scope.docs = [];
      $scope.activities = [];
      $scope.verified = false;

      $scope.init = function () {
        $scope.getUser();
      };

      $scope.getUser = function () {
        var abs = $location.absUrl();
        var id = abs.match(/.*\/(\d+)$/);
        id = id[1];

        $http.get('/api/user/' + id)
          .success(function (data) {
            $scope.user = angular.copy(data);
            $scope.meta = angular.copy(data.user_meta);

            angular.forEach(data.docs, function (doc) {
              $scope.docs.push(doc);
            });

            angular.forEach(data.comments, function (comment) {
              comment.label = 'comment';
              $scope.activities.push(comment);
            });

            angular.forEach(data.annotations, function (annotation) {
              annotation.label = 'annotation';
              $scope.activities.push(annotation);
            });

            angular.forEach($scope.user.user_meta, function (meta) {
              var cont = true;

              if (meta.meta_key === 'verify' && meta.meta_value === 'verified' && cont) {
                $scope.verified = true;
                cont = false;
              }
            });

          }).error(function (data) {
            console.error("Unable to retrieve user: %o", data);
          });
      };

      $scope.showVerified = function () {
        if ($scope.user.docs && $scope.user.docs.length > 0) {
          return true;
        }

        return false;
      };

      $scope.activityOrder = function (activity) {
        return Date.parse(activity.created_at);
      };

    }
    ]);