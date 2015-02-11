angular.module('madisonApp.services')
  .factory('AuthService', ['$http',
    function ($http) {
      return {
        login: function (credentials) {
          var login = $http.post('/api/user/login', credentials);

          return login;
        },
        logout: function () {
          var logout = $http.get('/api/user/logout');

          //Is there a way to do this without refreshing the whole page?
          logout.then(function () {
            location.reload();
          });

          return logout;
        },
        signup: function (credentials) {
          var signup = $http.post('/api/user/signup', credentials);

          return signup;
        }
      };
    }]);