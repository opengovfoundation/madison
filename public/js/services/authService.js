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

      authService.saveUser = function (user) {
        return $http.put('/api/user/edit/' + user.id,
          {
            'password': user.password,
            'verify_request': user.verify_request,
            'email': user.email,
            'fname': user.fname,
            'lname': user.lname,
            'url': user.url,
            'phone': user.phone
          }).then(function (res) {
            console.log(res);
            authService.getUser();
          });
      };

      authService.login = function (credentials) {
        return $http.post('/api/user/login', credentials)
                    .then(function () {
                      authService.getUser();
                    });
      };

      authService.facebookLogin = function () {
        authService._oauthLogin('/api/user/facebook-login', 'Facebook');
      };

      authService.twitterLogin = function () {
        authService._oauthLogin('/api/user/twitter-login', 'Twitter');
      };

      authService.linkedinLogin = function () {
        authService._oauthLogin('/api/user/linkedin-login', 'LinkedIn');
      };

      authService._oauthLogin = function (url, name) {
        return $http.get(url)
          .then(function (res) {
            if (res.status === 200) {
              window.location.href = res.data.authUrl;
            } else {
              growl.error('There was an error logging in with ' + name + '.');
            }
          });
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
