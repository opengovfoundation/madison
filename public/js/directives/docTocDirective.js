angular.module('madisonApp.directives')
  .directive('docToc', ['$timeout',
    function ($timeout) {
      return {
        restrict: 'A',
        templateUrl: '/templates/directives/doc-toc.html',
        link: function ($scope, element) {
          $scope.headings = [];

          $scope.$on('docContentUpdated', function () {
            $timeout(function () {
              var doc_content = $('#doc_content');
              var headings = doc_content.find('h1,h2,h3,h4,h5,h6');

              if (headings.length > 0) {
                headings.each(function (i, elm) {
                  elm = $(elm);
                  // Set an arbitrary id.
                  // TODO: use a better identifier here - preferably a title-based slug
                  if (!elm.attr('id')) {
                    elm.attr('id', 'heading-' + i);
                  }

                  elm.addClass('anchor');
                  $scope.headings.push({'title': elm.text(), 'tag': elm.prop('tagName'), 'link': elm.attr('id')});
                });
                $scope.$emit('tocAdded', true);
              } else {
                $scope.$emit('tocAdded', false);
                element.remove();
              }

              /**
              * Copied from doc.js
              *   Needs to be cleaned up!
              */
              $('.affix-elm').each(function (i, elm) {
                elm = $(elm);
                var elmtop = 0;
                if (elm.data('offset-top')) {
                  elmtop = elm.data('offset-top');
                }
                var elmbottom = 0;
                if (elm.data('offset-bottom')) {
                  elmbottom = elm.data('offset-bottom');
                }

                elm.affix({
                  offset: {
                    top: elmtop,
                    bottom: elmbottom
                  }
                });
              });
            });
          }, 0, false);

          // Table of Contents hide/show toggle.
          $scope.tocShow = false;
          $scope.toggleToc = function () {
              $scope.tocShow = !$scope.tocShow;
          };
        }
      };
    }]);
