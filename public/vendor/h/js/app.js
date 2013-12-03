(function() {
  var configure, imports;

  imports = ['bootstrap', 'ngRoute', 'h.controllers', 'h.directives', 'h.app_directives', 'h.displayer', 'h.flash', 'h.filters', 'h.services'];

  configure = [
    '$routeProvider', '$sceDelegateProvider', function($routeProvider, $sceDelegateProvider) {
      $routeProvider.when('/editor', {
        controller: 'EditorController',
        templateUrl: 'editor.html'
      });
      $routeProvider.when('/viewer', {
        controller: 'ViewerController',
        reloadOnSearch: false,
        templateUrl: 'viewer.html'
      });
      $routeProvider.when('/page_search', {
        controller: 'SearchController',
        reloadOnSearch: false,
        templateUrl: 'page_search.html'
      });
      $routeProvider.otherwise({
        redirectTo: '/viewer'
      });
      if (window.location.href.match(/^chrome-extension:\/\//)) {
        return $sceDelegateProvider.resourceUrlWhitelist(['self', '.*']);
      }
    }
  ];

  angular.module('h', imports, configure);

}).call(this);
