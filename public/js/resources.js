angular.module('madisonApp.resources', [])
  .factory("Doc", function ($resource) {
    return $resource("/api/docs/:id");
  })
  .factory("Group", function ($resource) {
    return $resource("/api/groups/:id");
  });