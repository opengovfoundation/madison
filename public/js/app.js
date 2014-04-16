window.$ = require('jquery');
window.jQuery = window.$;
window.Markdown = require('pagedown');


require('select2-browserify');
require('underscore');
require('angular');
require('angular-bootstrap');
require('angular-animate');
require('../../bower_components/angular-ui/build/angular-ui.min.js');
require('../../node_modules/twitter-bootstrap-3.0.0/dist/js/bootstrap.min.js');

//Require custom angular modules
require('./controllers');
require('./dashboardControllers');
require('./services');
require('./directives');
require('./filters');

window.getAnnotationService = require('./annotationServiceGlobal');

var imports = [
    'madisonApp.filters',
    'madisonApp.services',
    'madisonApp.directives',
    'madisonApp.controllers',
    'madisonApp.dashboardControllers',
    'ui',
    'ui.bootstrap',
    'ngAnimate',
  ];

var app = angular.module('madisonApp', imports, function ($interpolateProvider) {
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
  });
