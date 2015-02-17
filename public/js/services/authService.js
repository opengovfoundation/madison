/*jslint white:true */
/*global location */
angular.module('madisonApp.services')
  .factory('AuthService', ['$http', 'SessionService',
    function ($http, SessionService) {
      var authService = {};

      authService.login = function (credentials) {
        return $http.post('/api/user/login', credentials)
                    .then(function () {
                      //Get current user
                      $http.get('/api/user/current')
                        .success(function (data) {
                          var key;
                          var user = {
                            loggedin: function () {
                              return this.id !== undefined;
                            }
                          };

                          //Copy all user attributes
                          for (key in data.user) {
                            if (data.user.hasOwnProperty(key)) {
                              user[key] = data.user[key];
                            }
                          }

                          SessionService.create(user);
                        });
                    });
      };

      authService.isAuthenticated = function () {
        return !!SessionService.user.id;
      };

      authService.isAuthorized = function (authorizedRoles) {
        if(!angular.isArray(authorizedRoles)) {
          authorizedRoles = [authorizedRoles];
        }

        return (authService.isAuthenticated() && authorizedRoles.indexof(SessionService.user.role) !== -1);
      };

      authService.logout = function () {
        var logout = $http.get('/api/user/logout');

          //Is there a way to do this without refreshing the whole page?
          logout.then(function () {
            location.reload();
          });

          return logout;
      };

      authService.signup = function (credentials) {
        var signup = $http.post('/api/user/signup', credentials);

        return signup;
      };

      return authService;
    }]);