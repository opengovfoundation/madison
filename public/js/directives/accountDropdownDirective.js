angular.module('madisonApp.directives')
  .directive('accountDropdown', ['AuthService', 'SessionService', '$location', 'growl',
    function (AuthService, SessionService, $location, growl) {
      return {
        scope: true,
        link: function (scope) {
          scope.$on('sessionChanged', function () {
            scope.user = SessionService.user;
          });

          scope.logout = function () {
            var logout = AuthService.logout();

            logout.then(function () {
              growl.success('You have been successfully logged out.');
              AuthService.getUser();
              $location.path('/');
            });
          };
        },
        templateUrl: '/templates/partials/account-dropdown.html'
      };
    }]);