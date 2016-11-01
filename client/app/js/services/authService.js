/*global location */
/*global window */
angular.module('madisonApp.services')
  .factory('AuthService', ['$http', 'SessionService', 'USER_ROLES', 'growl',
    function ($http, SessionService, USER_ROLES, growl) {
      "use strict";
      var authService = {};

      authService.setUser = function (user) {
        SessionService.create(user);
      };

      authService.getUser = function () {
        var user = {};
        var groups = [];
        var activeGroupId = null;

        return $http.get('/api/user/current')
          .then(function (res) {
            var key;

            if (Object.getOwnPropertyNames(res.data).length > 0) {
              for (key in res.data.user) {
                if (res.data.user.hasOwnProperty(key)) {
                  user[key] = res.data.user[key];
                }
              }

              groups = res.data.groups;
              activeGroupId = res.data.activeGroupId;
            } else {
              user = null;
              groups = null;
              activeGroupId = null;
            }

            SessionService.create(user, groups, activeGroupId);
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
            authService.getUser();
          });
      };

      authService.setActiveGroup = function (groupId) {
        return $http.post('/api/groups/active/' + groupId)
          .then(function () {
            authService.getUser();
          });
      };

      authService.removeActiveGroup = function () {
        return $http.post('/api/groups/active/0')
          .then(function () {
            authService.getUser();
          });
      };

      authService.getMyDocs = function () {
        return $http.get('/api/user/' + SessionService.user.id + '/docs')
          .success(function (data) {
            SessionService.setDocs(data);
          }).error(function (response) {
            growl.error('There was an error retrieving your documents.');
            console.error(response);
          });
      };

      authService.login = function (credentials) {
        return $http.post('/api/user/login', credentials)
                    .then(function () {
                      authService.getUser();
                    });
      };

      authService.isAuthenticated = function () {
        return !!SessionService.user;
      };

      authService.isAuthorized = function (authorizedRoles) {
        var authorized = false;

        if (!angular.isArray(authorizedRoles)) {
          authorizedRoles = [authorizedRoles];
        }

        //If everyone's allowed, or the user is an admin, return true
        if (authorizedRoles.indexOf(USER_ROLES.all) !== -1) {
          authorized = true;
        } else if (!authService.isAuthenticated()) { //If the user isn't authenticated
          //Check guest role
          if (authorizedRoles.indexOf(USER_ROLES.guest) !== -1){
            authorized = true;
          }

        } else { //If the user is authenticated
          //Check Basic role
          if (authorizedRoles.indexOf(USER_ROLES.basic) !== -1) {
            authorized = true;
          }

          //Check Group Member Role
          if (authorizedRoles.indexOf(USER_ROLES.groupMember) !== -1 && SessionService.groups.length > 0) {
            authorized = true;
          }

          //Allow admins access to all routes
          if (SessionService.user.admin === true) {
            authorized = true;
          }
        }

        return authorized;
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
