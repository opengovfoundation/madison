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
        url: '/api/docs/:id/content?format=:format&page=:page',
        params: {
          id: '@id',
          page: '@page',
          format: '@format'
        }
      },
      getActivity: {
        method: 'GET',
        url: '/api/docs/:id/activity',
        params: {
          id: '@id'
        }
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
