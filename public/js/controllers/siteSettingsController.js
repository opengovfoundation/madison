angular.module('madisonApp.controllers')
  .controller('SiteSettingsController', function ($scope, $http) {
    $scope.featuredOptions = {
      minimumInputLength: 3,
      multiple: false,
      closeOnSelect: true,
      query: function (options) {
        var data = { results: [] };
        var i;

        //TODO: This should be called via the Doc resource
        $http.get('/api/docs/?title=' + options.term)
          .then(function (response) {
            for (i = 0; i < response.data.length; i++) {
              data.results.push({
                id: response.data[i].id,
                text: response.data[i].title
              });
            }
            options.callback(data);
          });
      }
    };
  });
