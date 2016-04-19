angular.module('madisonApp.resources')
.factory('Page', function($resource) {
  var Page = $resource('/api/pages/:id', [], {
    getContent: {
      method: 'GET',
      url: '/api/pages/:id/content',
      params: { id: '@id' }
    }
  });

  return Page;
});
