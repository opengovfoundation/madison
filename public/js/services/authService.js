/*jslint white:true */
/*global location */
/*global window */
angular.module('madisonApp.services')
  .factory('AuthService', ['$http', 'SessionService', 'USER_ROLES', 'growl',
    function ($http, SessionService, USER_ROLES, growl) {
      var authService = {};

      authService.setUser = function (user) {
        SessionService.create(user);
      };

      authService.getUser = function () {
        var user = {};

        return $http.get('/api/user/current')
          .then(function (res) {
            var key;

            if (Object.getOwnPropertyNames(res.data).length > 0) {
              for (key in res.data.user) {
                if (res.data.user.hasOwnProperty(key)) {
                  user[key] = res.data.user[key];
                }
              }
            } else {
              user = null;
            }

            SessionService.create(user);
          });
      };

      authService.login = function (credentials) {
        return $http.post('/api/user/login', credentials)
                    .then(function () {
                      authService.getUser();
                    });
      };

      authService.facebookLogin = function () {
        return $http.get('/api/user/facebook-login')
          .then(function (res) {
            if(res.status === 200) {
              window.location.href = res.data.authUrl;
            } else {
              growl.error('There was an error logging in with Facebook.');
            }
          });
      };

      authService.twitterLogin = function () {
        return $http.get('/api/user/twitter-login')
          .then(function (res) {
            if(res.status === 200) {
              window.location.href = res.data.authUrl;
            } else {
              growl.error('There was an error logging in with Twitter.');
            }
          });
      };

      authService.linkedinLogin = function () {
        console.log('Still waiting on this one...');
      };

      authService.isAuthenticated = function () {
        return !!SessionService.user;
      };

      authService.isAuthorized = function (authorizedRoles) {
        if(!angular.isArray(authorizedRoles)) {
          authorizedRoles = [authorizedRoles];
        }

        //If everyone's allowed, or the user is an admin, return true
        if(authorizedRoles.indexOf(USER_ROLES.all) !== -1){
          return true;
        }
        
        return (authService.isAuthenticated() && authorizedRoles.indexOf(SessionService.user.role) !== -1);
      };

      authService.logout = function () {
        var logout = $http.get('/api/user/logout');

          SessionService.destroy();

          return logout;
      };

      authService.signup = function (credentials) {
        var signup = $http.post('/api/user/signup', credentials);

        return signup;
      };

      return authService;
    }]);