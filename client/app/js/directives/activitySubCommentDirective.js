angular.module('madisonApp.directives')
  .directive('activitySubComment', ['growl', '$anchorScroll', '$timeout', function (growl, $anchorScroll, $timeout) {
    return {
      restrict: 'A',
      transclude: true,
      templateUrl: '/templates/activity-sub-comment.html',
      compile: function () {
        return {
          post: function (scope, element, attrs) {
            var commentLink = element.find('.subcomment-link').first();

            var basePath = window.location.href;
            var firstHashIndex = basePath.indexOf('#');
            if (firstHashIndex !== -1) {
              var secondHashIndex = basePath.indexOf('#', firstHashIndex + 1);
              if (secondHashIndex !== -1) basePath = basePath.subString(0, secondHashIndex);
            }

            var linkPath = window.getBasePath() + '#annsubcomment_' + attrs.subCommentId;

            $(commentLink).attr('data-clipboard-text', linkPath);

            var client = new ZeroClipboard(commentLink);

            client.on('aftercopy', function (event) {
              scope.$apply(function () {
                growl.success("Link copied to clipboard.");
              });
            });

            $timeout(function () {
              $anchorScroll();
            }, 0);
          }
        };
      }
    };
  }]);
