_ = require('underscore');
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
  });