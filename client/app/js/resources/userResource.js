angular.module('madisonApp.resources')
.factory('User', function($resource) {
  return $resource('/api/user/:id', {
    id: '@id'
  }, {
    update: {
      method: 'PUT'
    }
  });
});
