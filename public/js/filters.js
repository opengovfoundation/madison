angular.module('madisonApp.filters', [])
  .filter('parseDate', function () {
    return function (date) {
      return Date.parse(date);
    };
  }).filter('toArray', function () {
    return function (obj) {
      if (!(obj instanceof Object)) {
        return obj;
      }
      return _.map(obj, function (val, key) {
        val.$key = key;
        return val;
      });
    };
  }).filter('gravatar', function () {
    return function (email) {
      var hash = '';

      if (email !== undefined) {
        hash = CryptoJS.MD5(email.toLowerCase());
      }


      return hash;
    };
  }).filter('getById', function () {
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