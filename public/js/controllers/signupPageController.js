angular.module('madisonApp.controllers')
  .controller('SignupPageController', ['$scope', '$state', 'AuthService', 'UserService', 'growl',
    function ($scope, $state, AuthService, UserService, growl) {
      $scope.signup = function () {
        var signup = AuthService.signup($scope.credentials);

        signup.success(function (response) {
          $scope.credentials = {fname: "", lname: "", email: "", password: ""};
          UserService.getUser();
          $state.go('index');
          growl.addSuccessMessage("Welcome to Madison!  We just sent you an email.  Please click on the activation link to log in.");
        })
          .error(function (response) {
            console.error(response);
            if (!response.messages) {
              growl.addErrorMessage("There was an error signing you up.  Check your console for details.");
            }
          });
      };
    }]);