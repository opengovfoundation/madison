angular.module('madisonApp.directives')
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
  });