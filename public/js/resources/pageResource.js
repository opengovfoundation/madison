angular.module('madisonApp.resources')
.factory('Page', function($resource) {
  var Page = $resource('/api/pages/:id');

  return Page;
});
