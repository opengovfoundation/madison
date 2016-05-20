angular.module('madisonApp.filters')
  .filter('parseDate', function () {
    return function (date) {
      // This format is universally compatible between browsers
      return Date.parse(moment(date).format('MMMM D, YYYY HH:mm:ss'));
    };
  });
