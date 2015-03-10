angular.module('madisonApp.controllers')
  .controller('DocumentTocController', ['$scope',
    function ($scope) {
      $scope.headings = [];
      // For now, we use the simplest possible method to render the TOC -
      // just scraping the content.  We could use a real API callback here
      // later if need be.  A huge stack of jQuery follows. 
      $scope.$on('docContentUpdated', function () {
        var headings = $('#doc_content').find('h1,h2,h3,h4,h5,h6');

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
        } else {
          $('#toc-column').remove();
          var container = $('#content').parent();
          container.removeClass('col-md-6');
          container.addClass('col-md-9');
        }
      });
    }]);