// Hacks to get build process working.
// TODO: These need to be cleaned up.
window.$ = require('jquery');
window.jQuery = window.$;
window.Markdown = require('pagedown');

select2 = require('select2-browserify');

require('underscore');

require('angular');
require('angular-bootstrap');
require('angular-animate');
require('../bower_components/angular-ui/build/angular-ui.min.js');

//require('../bower_components/angular-select2/dist/angular-select2.min.js');

//Require custom angular modules
require('./controllers');
require('./services');
require('./directives');
require('./filters');
window.getAnnotationService = require('./annotationServiceGlobal');
require('./annotator-madison');

imports = [
    'madisonApp.filters',
    'madisonApp.services',
    'madisonApp.directives',
    'madisonApp.controllers',
    'ui',
    'ui.bootstrap',
    'ngAnimate',
];

var app = angular.module('madisonApp', imports, function ($interpolateProvider){
    $interpolateProvider.startSymbol('<%');
    $interpolateProvider.endSymbol('%>');
});