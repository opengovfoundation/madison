angular.module('madisonApp.controllers')
  .controller('SignupPageController', ['$scope', '$state', 'AuthService',
    'growl', '$translate', 'pageService', 'SITE',
    function ($scope, $state, AuthService, growl, $translate, pageService, SITE) {
      pageService.setTitle($translate.instant('content.signup.title',
        {title: SITE.name}));

      $scope.signup = function () {
        var signup = AuthService.signup($scope.credentials);

        signup.success(function () {
          $scope.credentials = {fname: "", lname: "", email: "", password: ""};
          AuthService.getUser();
          $state.go('index');
          growl.success($translate.instant('form.signup.success'));
        })
          .error(function (response) {
            console.error(response);
            if (!response.messages) {
              growl.error($translate.instant('form.signup.error'));
            }
          });
      };
    }]);
