(function() {
  var annotation;

  annotation = [
    '$filter', 'annotator', function($filter, annotator) {
      return {
        link: function(scope, elem, attrs, controller) {
          if (controller == null) {
            return;
          }
          elem.bind({
            keydown: function(e) {
              if (e.keyCode === 13 && e.shiftKey) {
                return scope.save(e);
              }
            }
          });
          scope.$watch('model.$modelValue.id', function(id) {
            var _ref, _ref1;
            scope.thread = annotator.threading.idTable[id];
            scope.auth = {};
            scope.auth["delete"] = (scope.model.$modelValue != null) && (((_ref = annotator.plugins) != null ? _ref.Permissions : void 0) != null) ? annotator.plugins.Permissions.authorize('delete', scope.model.$modelValue) : true;
            return scope.auth.update = (scope.model.$modelValue != null) && (((_ref1 = annotator.plugins) != null ? _ref1.Permissions : void 0) != null) ? annotator.plugins.Permissions.authorize('update', scope.model.$modelValue) : true;
          });
          return scope.model = controller;
        },
        controller: 'AnnotationController',
        priority: 100,
        require: '?ngModel',
        restrict: 'C',
        scope: {
          mode: '@',
          replies: '@'
        },
        templateUrl: 'annotation.html'
      };
    }
  ];

  angular.module('h.app_directives', ['ngSanitize']).directive('annotation', annotation);

}).call(this);
