angular.module('madisonApp.directives')
  .directive('updateTitle', ['$rootScope', '$timeout',
    function ($rootScope, $timeout) {
      return {
        link: function (scope, element) {
          var listener = function (event, toState) {
            var title = "Madison";

            if (toState.data && toState.data.title) {
              title = toState.data.title;
            }

            $timeout(function () {
              element.text(title);
            }, 0, false);
          };

          $rootScope.$on('$stateChangeSuccess', listener);
        }
      };
    }]);