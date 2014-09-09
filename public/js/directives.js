/*global ZeroClipboard*/
/*global window*/
angular.module('madisonApp.directives', [])
  .directive('profileCompletionMessage', ['$http',
    function ($http) {
      return {
        restrict: 'A',
        templateUrl: '/templates/profile-completion-message.html',
        link: function (scope) {

          scope.updateEmail = function (newEmail, newPassword) {
            //Issue PUT request to update user
            $http.put('/api/user/' + scope.user.id + '/edit/email', {email: newEmail, password: newPassword})
              .success(function () {
                //Note: Growl message comes from server response
                scope.user.email = newEmail;
              }).error(function (data) {
                console.error("Error updating user email: %o", data);
              });
          };
        }
      };
    }])
  .directive('docComments', function () {
    return {
      restrict: 'AECM',
      templateUrl: '/templates/doc-comments.html'
    };
  })
  .directive('ngBlur', function () {
    return function (scope, elem, attrs) {
      elem.bind('blur', function () {
        scope.$apply(attrs.ngBlur);
      });
    };
  })
  .directive('docLink', function ($http, $compile) {
    function link(scope, elem, attrs) {
      $http.get('/api/docs/' + attrs.docId)
        .success(function (data) {
          var html = '<a href="/docs/' + data.slug + '">' + data.title + '</a>';
          var e = $compile(html)(scope);
          elem.replaceWith(e);
        }).error(function (data) {
          console.error("Unable to retrieve document %o: %o", attrs.docId, data);
        });
    }

    return {
      restrict: 'AECM',
      link: link
    };
  })
  .directive('docListItem', function () {
    return {
      restrict: 'A',
      templateUrl: '/templates/doc-list-item.html'
    };
  }).directive('annotationItem', ['growl', function (growl) {

    return {
      restrict: 'A',
      transclude: true,
      templateUrl: '/templates/annotation-item.html',
      compile: function () {
        return {
          post: function (scope, element, attrs) {
            var commentLink = element.find('.comment-link').first();
            var linkPath = window.location.origin + window.location.pathname + '#' + attrs.activityItemLink;
            $(commentLink).attr('data-clipboard-text', linkPath);

            var client = new ZeroClipboard(commentLink);

            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.addSuccessMessage("Link copied to clipboard.");
              });
            });
          }
        };
      }
    };
  }])
  .directive('commentItem', ['growl', function (growl) {

    return {
      restrict: 'A',
      transclude: true,
      templateUrl: '/templates/comment-item.html',
      compile: function () {
        return {
          post: function (scope, element, attrs) {
            var commentLink = element.find('.comment-link').first();
            var linkPath = window.location.origin + window.location.pathname + '#' + attrs.activityItemLink;
            $(commentLink).attr('data-clipboard-text', linkPath);

            var client = new ZeroClipboard(commentLink);

            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.addSuccessMessage("Link copied to clipboard.");
              });
            });
          }
        };
      }
    };
  }])
  .directive('subcommentLink', ['growl', '$anchorScroll', '$timeout', function (growl, $anchorScroll, $timeout) {
    return {
      restrict: 'A',
      template: '<span class="glyphicon glyphicon-link" title="Copy link to clipboard"></span>',
      compile: function () {
        return {
          post: function (scope, element, attrs) {
            var commentLink = element;
            var linkPath = window.location.origin + window.location.pathname + '#subcomment_' + attrs.subCommentId;

            $(commentLink).attr('data-clipboard-text', linkPath);

            var client = new ZeroClipboard(commentLink);

            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.addSuccessMessage("Link copied to clipboard.");
              });
            });

            $timeout(function () {
              $anchorScroll();
            }, 0);
          }
        };
      }
    };
  }])
  .directive('activitySubComment', ['growl', '$anchorScroll', '$timeout', function (growl, $anchorScroll, $timeout) {
    return {
      restrict: 'A',
      transclude: true,
      templateUrl: '/templates/activity-sub-comment.html',
      compile: function () {
        return {
          post: function (scope, element, attrs) {
            var commentLink = element.find('.subcomment-link').first();
            var linkPath = window.location.origin + window.location.pathname + '#annsubcomment_' + attrs.subCommentId;

            $(commentLink).attr('data-clipboard-text', linkPath);

            var client = new ZeroClipboard(commentLink);

            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.addSuccessMessage("Link copied to clipboard.");
              });
            });

            $timeout(function () {
              $anchorScroll();
            }, 0);
          }
        };
      }
    };
  }]).directive('socialLogin', [ function () {
    return {
      restrict: 'A',
      scope: {
        message: '@message'
      },
      templateUrl: '/templates/social-login.html'
    };
  }]);