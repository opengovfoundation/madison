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
      },
      getFeaturedDocs: {
        method: 'GET',
        url: '/api/docs/featured',
        isArray:true
      },
      removeFeatured: {
        method: 'DELETE',
        url: '/api/docs/featured/:id',
        params: {id: '@id'}
      },
      getDocCount: {
        method: 'GET',
        url: '/api/docs/count'
      }
    });

    return Doc;
  });
