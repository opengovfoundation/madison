angular.module('madisonApp.controllers')
  .controller('PasswordResetController', ['$scope', '$http', '$state',
    '$translate', 'growl', 'pageService', 'SITE',
    function ($scope, $http, $state, $translate, growl, pageService, SITE) {
      $translate('content.resetpassword.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.reset = function () {
        $http.post('/api/password/remind', {email: $scope.email})
          .success(function () {
            $state.go('login');
          }).error(function (response) {
            console.error(response);
          });
      };

    }]);
