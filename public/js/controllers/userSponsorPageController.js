angular.module('madisonApp.controllers')
.controller('UserSponsorPageController', ['$scope', '$http', '$location',
  'SessionService', 'AuthService', 'growl', 'growlMessages', 'PROVINCES',
  '$translate', 'pageService', 'SITE',
	function ($scope, $http, $location, SessionService, AuthService, growl,
    growlMessages, PROVINCES, $translate, pageService, SITE) {

    $translate('content.sponsorrequest.title', {title: SITE.name}).then(function(translation) {
      pageService.setTitle(translation);
    });

    $scope.provinces = [];
    $scope.formData = {};

    // Hardcoding this for now, easily hacked to be dynamic for multiple
    // countries.

    var provinces = PROVINCES.US;

    $scope.provincesInit = function(provinces) {
      // Angular does not keep the order assigned, so we have to transform into an
      // array.
      angular.forEach(provinces, function(name, value) {
        $scope.provinces.push({
          'name': name,
          'value': value
        });
      });
    };
    $scope.provincesInit(provinces);

    $scope.loadData = function() {
      $http.get('/api/user/current')
      .success(function (data) {
        var formFields = [
          'address1',
          'address2',
          'city',
          'state',
          'postal_code',
          'phone'
        ];

        for(var i in formFields) {
          var field = formFields[i];
          $scope.formData[field] = data.user[field];
        }
      })
      .error(function (data) {
        growl.error('Error getting data.');
        console.log('Error getting data.', data);
      });
    };
    $scope.loadData();

    $scope.saveUser = function () {
      growlMessages.destroyAllMessages();

      $http.put('/api/user/sponsor', $scope.formData)
      .success(function (data) {
        $location.path('/user/edit/' + $scope.user.id).replace();
        growl.info('Your contact info was updated.  We\'ll be in touch soon!');
      })
      .error(function (data) {
        console.error("Error posting comment: %o", data);
      });

    };

  }
]);
