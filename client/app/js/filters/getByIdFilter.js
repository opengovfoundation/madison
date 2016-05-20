angular.module('madisonApp.filters')
  .filter('getById', function () {
    return function (input, id) {
      var i = 0;
      var len = input.length;
      for (i; i < len; i++) {
        if (+input[i].id === +id) {
          return input[i];
        }
      }

      return null;
    };
  });