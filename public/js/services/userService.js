angular.module('madisonApp.services')
  .factory('UserService', ['$rootScope', '$http', '$window',
    function ($rootScope, $http, $window) {
      var UserService = {};

      UserService.user = {
        loggedin: function () {
          return this.id !== undefined;
        }
      };

      UserService.getUser = function () {
        UserService.exists = $http.get('/api/user/current')
          .success(function (data) {
            var key;

            for (key in data.user) {
              if (data.user.hasOwnProperty(key)) {
                UserService.user[key] = data.user[key];
              }
            }

            $window.user = UserService.user;
            $rootScope.$broadcast('userUpdated');
          }).error(function (data) {
            console.error(data);
          });
      };

      UserService.getGroups = function () {
        UserService.exists.then(function () {
          $http.get('/api/user/' + UserService.user.id + '/groups')
            .success(function (data) {
              UserService.groups = data;
              $rootScope.$broadcast('groupsUpdated');
            }).error(function (data) {
              console.error(data);
            });
        });

      };

      return UserService;
    }]);