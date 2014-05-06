window.jQuery = window.$;

var imports = [
    'madisonApp.filters',
    'madisonApp.services',
    'madisonApp.directives',
    'madisonApp.controllers',
    'madisonApp.dashboardControllers',
    'ui',
    'ui.bootstrap',
    'ngAnimate'
  ];

var app = angular.module('madisonApp', imports, function ($interpolateProvider) {});

window.console = window.console || {};
window.console.log = window.console.log || function () {};
