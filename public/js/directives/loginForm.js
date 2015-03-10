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
              $scope.credentials = {email: "", password: "", remember: false};
              growl.success("You have been logged in");
              $state.go($state.current, null, { reload: true});
            });
          };
        }
      };

    }]);