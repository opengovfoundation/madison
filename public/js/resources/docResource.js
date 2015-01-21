angular.module('madisonApp.resources')
  .factory("Doc", function ($resource) {
    var Doc = $resource("/api/docs/:id", [], {
      getDocBySlug: {
        method: 'GET',
        url: '/api/docs/slug/:slug',
        params: {slug: '@slug'}
      },
      getDocContent: {
        method: 'GET',
        url: '/api/docs/:id/content',
        params: {id: '@id'}
      }
    });

    return Doc;
  });