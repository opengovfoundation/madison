angular.module('madisonApp.controllers')
  .controller('SignupPageController', ['$scope', '$state', 'AuthService', 'growl',
    function ($scope, $state, AuthService, growl) {
      $scope.signup = function () {
        var signup = AuthService.signup($scope.credentials);

        signup.success(function () {
          $scope.credentials = {fname: "", lname: "", email: "", password: ""};
          AuthService.getUser();
          $state.go('index');
          growl.success("Welcome to Madison!  We just sent you an email.  Please click on the activation link to log in.");
        })
          .error(function (response) {
            console.error(response);
            if (!response.messages) {
              growl.error("There was an error signing you up.  Check your console for details.");
            }
          });
      };
    }]);