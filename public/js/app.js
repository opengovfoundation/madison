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
    'ui.router',
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

app.config(['growlProvider', '$httpProvider', '$stateProvider', '$urlRouterProvider', function (growlProvider, $httpProvider, $stateProvider, $urlRouterProvider) {
    //Set up growl notifications
  growlProvider.messagesKey("messages");
  growlProvider.messageTextKey("text");
  growlProvider.messageSeverityKey("severity");
  $httpProvider.responseInterceptors.push(growlProvider.serverMessagesInterceptor);
  growlProvider.onlyUniqueMessages(true);
  growlProvider.globalTimeToLive(5000);

  $urlRouterProvider.otherwise('/');

  $stateProvider
    .state('index', {
      url: "/",
      controller: "HomePageController",
      templateUrl: "/templates/pages/home.html",
      data: {title: "Madison Home"}
    })
    .state('faq', {
      url: "/faq",
      templateUrl: "/templates/pages/faq.html",
      data: {title: "Frequently Asked Questions"}
    })
    .state('about', {
      url: "/about",
      templateUrl: "/templates/pages/about.html",
      data: {title: "About Madison"}
    })
    .state('user-notification-settings', {
      url: "/user/edit/:user/notifications",
      controller: "UserNotificationsController",
      templateUrl: "/templates/pages/user-notification-settings.html",
      data: {title: "Notification Settings"}
    });
}]);

app.config(function ($locationProvider) {
  $locationProvider.html5Mode(true);
});

window.console = window.console || {};
window.console.log = window.console.log || function () {};
