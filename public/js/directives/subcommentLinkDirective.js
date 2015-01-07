angular.module('madisonApp.directives')
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