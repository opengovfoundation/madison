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
  ];

var app = angular.module('madisonApp', imports);

app.config(function ($locationProvider) {
    $locationProvider.html5Mode(true);
});

window.console = window.console || {};
window.console.log = window.console.log || function () {};
