angular.module('madisonApp.directives')
  .directive('loginForm', [ 'AuthService', 'loginPopupService', 'growl', '$state',
    function (AuthService, loginPopupService, growl, $state) {
      return {
        restrict: 'A',
        templateUrl: '/templates/partials/loginForm.html',
        link: function (scope, element) {
          element.css('top', loginPopupService.top);
          element.css('left', loginPopupService.left);
        },
        controller: function ($scope) {

          $scope.switchState = function (stateString) {
            $scope.state = stateString;
          };

          $scope.login = function (email, password, remember) {
            var credentials = {email: email, password: password, remember: remember};

            var login = AuthService.login(credentials);

            login.then(function () {
              loginPopupService.closeLoginForm();
              growl.success("You have been logged in");
              $state.go($state.current, null, { reload: true});
            });
          };

          $scope.signup = function (fname, lname, email, password) {
            var credentials = {fname: fname, lname: lname, email: email, password: password};

            var signup = AuthService.signup(credentials);

            signup.then(function () {
              growl.success("Welcome to Madison!  We just sent you an email.  Please click on the activation link to log in.");
              $state.go($state.current, null, {reload: true});
              loginPopupService.closeLoginForm();
            });
          };

          $scope.closePopup = function () {
            loginPopupService.closeLoginForm();
          };
        }
      };

    }]);