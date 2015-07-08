angular.module('madisonApp.filters')
  .filter('gravatar', function () {
    return function (email) {
      var hash = '';

      if (email !== undefined) {
        hash = CryptoJS.MD5(email.toLowerCase());
      }

      return hash.toString(CryptoJS.enc.Hex);
    };
  });