angular.module('madisonApp.services')
  .factory('AuthService', ['$http', '$sanitize',
    function ($http, $sanitize) {
      return {
        login: function (credentials) {
          var login = $http.post('/api/user/login', credentials);

          return login;
        },
        logout: function () {
          var logout = $http.get('/api/user/logout');

          return logout;
        },
        signup: function (credentials) {
          var signup = $http.post('/api/user/signup', credentials);

          return signup;
        }
      };
    }]);