angular.module('madisonApp.providers')
.provider('runtimeStates', ['$stateProvider', function($stateProvider) {
  this.$get = function() {
    return {
      addState: function(name, state) {
        $stateProvider.state(name, state);
      }
    };
  };
}]);
