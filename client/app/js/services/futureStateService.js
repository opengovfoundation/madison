angular.module('madisonApp.services')
.provider('futureStateService', ['$futureStateProvider',
function($futureStateProvider) {

  this.$get = function() {
    return {
      addFutureState: function(page) {
        $futureStateProvider.futureState({
          type: 'customPage',
          name: page.url,
          url: page.url,
          page: page
        });
      }
    };
  };

}]);
