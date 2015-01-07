angular.module('madisonApp.directives')
  .directive('accountDropdown', ['UserService', 'AuthService', '$location', 'growl',
    function (UserService, AuthService, $location, growl) {
      return {
        scope: true,
        link: function (scope, element, attrs) {
          scope.$watch(function () {
            return UserService.user;
          }, function (newVal) {
            scope.user = newVal;
          });

          scope.logout = function () {
            var logout = AuthService.logout();

            logout.then(function () {
              growl.addSuccessMessage('You have been successfully logged out.');
              UserService.getUser();
              $location.path('/');
            });
          };
        },
        templateUrl: '/templates/partials/account-dropdown.html'
      };
    }]);