/*global window*/
/*global history*/
window.jQuery = window.$;

var user;

var imports = [
  'madisonApp.constants',
  'madisonApp.filters',
  'madisonApp.services',
  'madisonApp.resources',
  'madisonApp.directives',
  'madisonApp.controllers',
  'ui',
  'ui.router',
  'ui.bootstrap',
  'ui.bootstrap.datetimepicker',
  'ngAnimate',
  'ngSanitize',
  'angular-growl',
  'ngResource',
  'ipCookie'
];

try {
  var app = angular.module('madisonApp', imports);
} catch (err) {
  console.log('Caught Error: ' + err);
}

if (!history.pushState) {
  if (window.location.hash) {
    if (window.location.pathname !== '/') {
      window.location.replace('/#' + window.location.hash.substr(1));
    } else {
      window.location.replace('/#' + window.location.pathname);
    }
  }
}

try {
  app.config(['growlProvider', '$httpProvider',
    function (growlProvider, $httpProvider) {
      //Set up growl notifications
      growlProvider.messagesKey("messages");
      growlProvider.messageTextKey("text");
      growlProvider.messageSeverityKey("severity");
      growlProvider.onlyUniqueMessages(true);
      growlProvider.globalTimeToLive(3000);
      $httpProvider.interceptors.push(growlProvider.serverMessagesInterceptor);
    }]);
} catch (err) {
  console.error(err);
}

app.config(['$locationProvider',
  function ($locationProvider) {
    $locationProvider.html5Mode(true);
  }]);

app.run(function (AuthService, AUTH_EVENTS, $rootScope, $state, growl) {
  AuthService.setUser(user);

  $rootScope.$on('$stateChangeStart', function (event, next) {
    var authorizedRoles = next.data.authorizedRoles;

    if (!AuthService.isAuthorized(authorizedRoles)) {
      event.preventDefault();

      if (AuthService.isAuthenticated()) {
        //user is not allowed
        $rootScope.$broadcast(AUTH_EVENTS.notAuthorized);
      } else {
        //user is not logged in
        $rootScope.$broadcast(AUTH_EVENTS.notAuthenticated);
      }
    }
  });

  $rootScope.$on(AUTH_EVENTS.notAuthorized, function () {
    growl.error('You are not authorized to view that page');
  });

  $rootScope.$on(AUTH_EVENTS.notAuthenticated, function () {
    growl.error('You must be logged in to view that page');
    $state.go('index');
  });
});

//Manually bootstrap app because we want to get data before running
angular.element(document).ready(function () {
  var initInjector = angular.injector(['ng']);
  var $http = initInjector.get('$http');
  user = {};

  $http.get('/api/user/current')
    .success(function (res) {
      var key;

      if (!$.isEmptyObject(res) && Object.getOwnPropertyNames(res.user).length > 0) {
        for (key in res.user) {
          if (res.user.hasOwnProperty(key)) {
            user[key] = res.user[key];
          }
        }
      } else {
        user = null;
      }
      angular.bootstrap(document, ['madisonApp']);
    });
});

window.console = window.console || {};
window.console.log = window.console.log || function () {return; };
