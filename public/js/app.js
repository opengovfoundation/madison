/*global window*/
/*global history*/
window.jQuery = window.$;

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

app.run(function (AuthService) {
  AuthService.getUser();
});

window.console = window.console || {};
window.console.log = window.console.log || function () {return; };
