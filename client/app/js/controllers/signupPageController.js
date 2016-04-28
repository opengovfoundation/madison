angular.module('madisonApp.controllers')
  .controller('SignupPageController', ['$scope', '$state', 'AuthService',
    'growl', 'growlMessages', '$translate', 'pageService', 'SITE',
    function ($scope, $state, AuthService, growl, growlMessages, $translate,
      pageService, SITE) {
      $translate('content.signup.title', {title: SITE.name}).then(function(translation) {
        pageService.setTitle(translation);
      });

      $scope.signup = function () {
        var signup = AuthService.signup($scope.credentials);
        growlMessages.destroyAllMessages();


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
