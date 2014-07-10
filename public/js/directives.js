/*global ZeroClipboard*/
/*global window*/
angular.module('madisonApp.directives', []).directive('docComments', function () {
  return {
    restrict: 'AECM',
    templateUrl: '/templates/doc-comments.html'
  };
}).directive('ngBlur', function () {
  return function (scope, elem, attrs) {
    elem.bind('blur', function () {
      scope.$apply(attrs.ngBlur);
    });
  };
}).directive('docLink', function ($http, $compile) {

  function link(scope, elem, attrs) {

    $http.get('/api/docs/' + attrs.docId)
      .success(function (data) {
        var html = '<a href="/docs/' + data.slug + '">' + data.title + '</a>';
        var e = $compile(html)(scope);
        elem.replaceWith(e);
      }).error(function (data) {
        console.error("Unable to retrieve document %o: %o", attrs.docId, data);
      });

  }

  return {
    restrict: 'AECM',
    link: link
  };
}).directive('docListItem', function () {
  return {
    restrict: 'A',
    templateUrl: '/templates/doc-list-item.html'
  };
}).directive('activityItem', ['growl', function (growl) {

  return {
    restrict: 'A',
    transclude: true,
    templateUrl: '/templates/activity-item.html',
    compile: function () {
      return {
        post: function (scope, element, attrs) {
          var commentLink = element.find('.comment-link').first();
          var linkPath = window.location.origin + window.location.pathname + '#' + attrs.activityItemLink;
          $(commentLink).attr('data-clipboard-text', linkPath);

          var client = new ZeroClipboard(commentLink);

          client.on('aftercopy', function (event) {
            scope.$apply(function () {
              growl.addSuccessMessage("Link copied to clipboard.");
            });
          });
        }
      };
    }
  };
}]).directive('activitySubComment', ['growl', '$anchorScroll', '$timeout', function (growl, $anchorScroll, $timeout) {
  return {
    restrict: 'A',
    transclude: true,
    templateUrl: '/templates/activity-sub-comment.html',
    compile: function () {
      return {
        pre: function () {

        },
        post: function (scope, element, attrs) {
          var commentLink = element.find('.subcomment-link').first();
          var linkPath = window.location.origin + window.location.pathname + '#subcomment_' + attrs.subCommentId;

          $(commentLink).attr('data-clipboard-text', linkPath);

          var client = new ZeroClipboard(commentLink);

          client.on('aftercopy', function (event) {
            scope.$apply(function () {
              growl.addSuccessMessage("Link copied to clipboard.");
            });
          });

          $timeout(function () {
            $anchorScroll();
          }, 0);
        }
      };
    }
  };
}]).directive('parseURL', function () {
  var urlPattern = /(http|ftp|https):\/\/[\w-]+(\.[\w-]+)+([\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/gi;
  return {    
    restrict: 'A',    
    require: 'ngModel',
    replace: true,   
    scope: { ngModel: '=ngModel' },
    link: function compile(scope, element, attrs, controller) {         
        scope.$watch('ngModel', function(value) {         
            angular.forEach(value.match(urlPattern), function(url) {
                value = value.replace(url, '<a href='+ url + '>' + url + '</a>');
            });
            element.html(value);        
          });                
    }
  }; 
});