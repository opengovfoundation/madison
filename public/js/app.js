window.jQuery = window.$;

var imports = [
    'madisonApp.filters',
    'madisonApp.services',
    'madisonApp.directives',
    'madisonApp.controllers',
    'madisonApp.dashboardControllers',
    'ui',
    'ui.bootstrap',
    'ui.bootstrap.datetimepicker',
    'ngAnimate',
    'ngCookies',
    'ngSanitize',
    'angular-growl'
  ];

var app = angular.module('madisonApp', imports);

app.config(['growlProvider', '$httpProvider', function (growlProvider, $httpProvider) {
    growlProvider.messagesKey("messages");
    growlProvider.messageTextKey("text");
    growlProvider.messageSeverityKey("severity");
    $httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
    growlProvider.onlyUniqueMessages(false);
    growlProvider.globalTimeToLive(5000);
}]);

app.config(function ($locationProvider) {
    $locationProvider.html5Mode(true);
});

window.console = window.console || {};
window.console.log = window.console.log || function () {};
