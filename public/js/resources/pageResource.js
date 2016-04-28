angular.module('madisonApp.resources')
.factory('Page', function($resource) {
  var Page = $resource('/api/pages/:id', [], {

    update: { method: 'PUT' },

    getContent: {
      method: 'GET',
      url: '/api/pages/:id/content?format=:format',
      params: {
        id: '@id',
        format: '@format'
      }
    },

    updateContent: {
      method: 'PUT',
      url: '/api/pages/:id/content',
      params: { id: '@id' }
    }

  });

  return Page;
});
