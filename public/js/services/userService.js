angular.module('madisonApp.services')
  .factory('UserService', ['$rootScope', '$http',
    function ($rootScope, $http) {
      var UserService = {};
      UserService.user = {};

      UserService.getUser = function () {
        UserService.exists = $http.get('/api/user/current')
          .success(function (data) {
            UserService.user = data.user;
            $rootScope.$broadcast('userUpdated');
          });
      };

      return UserService;
    }]);