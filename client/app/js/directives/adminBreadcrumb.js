angular.module('madisonApp.directives')
  .directive('adminBreadcrumb', ['$location',
    function ($location) {
      return {
        restrict: 'A',
        templateUrl: '/templates/directives/admin-breadcrumb.html',
        link: function (scope) {
          scope.crumbs = [];

          //Split the path
          var crumbs = $location.$$path.substring(1).split('/');
          var link = "", i = 0, crumb, label, current;

          //Ie. administrative-dashboard -> Administrative Dashboard
          function camelCase(match, group1, group2) {
            return " " + group2.toUpperCase();
          }

          //Loop through all crumbs but the last one
          for (i = 0; i < crumbs.length - 1; i++) {
            
            crumb = crumbs[i];
            link += "/" + crumb;
            label = crumb.toLowerCase().replace(/(^|-)(.)/g, camelCase);

            crumb = {link: link, label: label};
            scope.crumbs.push(crumb);
          }

          //Grab the last crumb
          current = crumbs.pop();

          //Create the label
          label = current.toLowerCase().replace(/(^|-)(.)/g, camelCase);

          //Append to the rest without link
          crumb = {label: label};
          scope.crumbs.push(crumb);
        }
      };
    }]);