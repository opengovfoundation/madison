angular.module('madisonApp.directives')
  .directive('loginForm', [ 'AuthService',
    function (AuthService) {
      
      return {
        restrict: 'A',
        templateUrl: '/templates/partials/loginForm.html',
        controller: function (scope) {
          scope.login = function () {
            console.log(scope);
          };
        }
      };

    }]);