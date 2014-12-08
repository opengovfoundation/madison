/*global window*/
window.jQuery = window.$;

var imports = [
    'madisonApp.filters',
    'madisonApp.services',
    'madisonApp.resources',
    'madisonApp.directives',
    'madisonApp.controllers',
    'madisonApp.dashboardControllers',
    'ui',
    'ui.bootstrap',
    'ui.bootstrap.datetimepicker',
    'ngAnimate',
    'ngCookies',
    'ngSanitize',
    'angular-growl',
    'ngResource',
    'angular-tour',
    'ngRoute',
    'ipCookie'
  ];

var app = angular.module('madisonApp', imports);

if(!history.pushState){
  if(window.location.hash){
    if(window.location.pathname !== '/'){
      window.location.replace('/#' + window.location.hash.substr(1));
    } else {
      window.location.replace('/#' + window.location.pathname);
    }
  }
}

app.config(['growlProvider', '$httpProvider', '$routeProvider', function (growlProvider, $httpProvider, $routeProvider) {
    //Set up growl notifications
  growlProvider.messagesKey("messages");
  growlProvider.messageTextKey("text");
  growlProvider.messageSeverityKey("severity");
  $httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
  growlProvider.onlyUniqueMessages(true);
  growlProvider.globalTimeToLive(5000);

  $routeProvider
    .when('/faq', {
      templateURL: "/templates/pages/faq.html",
      controller: "StaticPageController",
      title: "Frequently Asked Questions"
    })
    .when('/user/edit/:user/notifications', {
      templateUrl: "/templates/pages/user-notification-settings.html",
      controller: "UserNotificationsController",
      title: "Notification Settings"
    })
    .when('/', {
      templateUrl: "/templates/pages/home.html",
      controller: "HomePageController",
      title: "Madison"
    })
    .when('/about', {
      templateURL: "/templates/pages/about.html",
      controller: "StaticPageController",
      title: "About Madison"
    });
}]);

app.config(function ($locationProvider) {
  $locationProvider.html5Mode(true);
});

window.console = window.console || {};
window.console.log = window.console.log || function () {};
