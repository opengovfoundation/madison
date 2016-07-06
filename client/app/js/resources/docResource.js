angular.module('madisonApp.resources')
  .factory("Doc", function ($resource) {
    var Doc = $resource("/api/docs/:id", [], {
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
        url: '/api/docs/featured?featured_only=:featured_only',
        isArray:true,
        params: {
          featured_only: '@featured_only'
        }
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
