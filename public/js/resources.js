angular.module('madisonApp.resources', [])
  .factory("Doc", function ($resource) {
    return $resource("/api/docs/:id");
  });