angular.module('madisonApp.controllers')
  .controller('PasswordResetController', ['$scope', '$http', '$state',
    '$translate', 'pageService', 'SITE',
    function ($scope, $http, $state, $translate, pageService, SITE) {
      pageService.setTitle($translate.instant('content.intro.title',
        {title: SITE.name}));

      $scope.reset = function () {
        $http.post('/api/password/remind', {email: $scope.email})
          .success(function () {
            $state.go('login');
          }).error(function (response) {
            console.error(response);
          });
      };

    }]);
