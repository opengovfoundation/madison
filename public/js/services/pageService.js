angular.module('madisonApp.services')
  .factory('pageService', function() {
    var title = '';
    return {
       title: function() { return title; },
       setTitle: function(newTitle) { title = newTitle; }
    };
  });
