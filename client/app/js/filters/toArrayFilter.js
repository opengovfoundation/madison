angular.module('madisonApp.filters')
  .filter('toArray', function () {
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