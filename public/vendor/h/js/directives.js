(function() {
  var authentication, fuzzytime, markdown, notification, privacy, recursive, repeatAnim, resettable, slowValidate, streamviewer, tabReveal, tags, thread, userPicker, username, whenscrolled,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  authentication = function() {
    var base;
    base = {
      username: null,
      email: null,
      password: null,
      code: null
    };
    return {
      link: function(scope, elem, attr, ctrl) {
        return angular.extend(scope, base);
      },
      controller: [
        '$scope', 'authentication', function($scope, authentication) {
          var _this = this;
          $scope.$on('$reset', function() {
            return angular.extend($scope.model, base);
          });
          return $scope.submit = function(form) {
            if (!form.$valid) {
              return;
            }
            return authentication["$" + form.$name](function() {
              return $scope.$emit('success', form.$name);
            });
          };
        }
      ],
      scope: {
        model: '=authentication'
      }
    };
  };

  markdown = [
    '$filter', '$timeout', function($filter, $timeout) {
      return {
        link: function(scope, elem, attr, ctrl) {
          var input, output;
          if (ctrl == null) {
            return;
          }
          input = elem.find('textarea');
          output = elem.find('div');
          ctrl.$render = function() {
            input.val(ctrl.$viewValue || '');
            return scope.rendered = ($filter('converter'))(ctrl.$viewValue || '');
          };
          input.bind('blur change keyup', function() {
            ctrl.$setViewValue(input.val());
            return scope.$digest();
          });
          return scope.$watch('readonly', function(readonly) {
            ctrl.$render();
            if (!readonly) {
              return $timeout(function() {
                return input.focus();
              });
            }
          });
        },
        require: '?ngModel',
        restrict: 'E',
        scope: {
          readonly: '@',
          required: '@'
        },
        templateUrl: 'markdown.html'
      };
    }
  ];

  privacy = function() {
    var levels;
    levels = ['Public', 'Private'];
    return {
      link: function(scope, elem, attrs, controller) {
        if (controller == null) {
          return;
        }
        controller.$formatters.push(function(permissions) {
          if (permissions == null) {
            return;
          }
          if (__indexOf.call(permissions.read || [], 'group:__world__') >= 0) {
            return 'Public';
          } else {
            return 'Private';
          }
        });
        controller.$parsers.push(function(privacy) {
          var permissions, read, role;
          if (privacy == null) {
            return;
          }
          permissions = controller.$modelValue;
          if (privacy === 'Public') {
            if (permissions.read) {
              if (__indexOf.call(permissions.read, 'group:__world__') < 0) {
                permissions.read.push('group:__world__');
              }
            } else {
              permissions.read = ['group:__world__'];
            }
          } else {
            read = permissions.read || [];
            read = (function() {
              var _i, _len, _results;
              _results = [];
              for (_i = 0, _len = read.length; _i < _len; _i++) {
                role = read[_i];
                if (role !== 'group:__world__') {
                  _results.push(role);
                }
              }
              return _results;
            })();
            permissions.read = read;
          }
          return permissions;
        });
        scope.model = controller;
        return scope.levels = levels;
      },
      require: '?ngModel',
      restrict: 'E',
      scope: true,
      templateUrl: 'privacy.html'
    };
  };

  recursive = [
    '$compile', '$timeout', function($compile, $timeout) {
      return {
        compile: function(tElement, tAttrs, transclude) {
          var attachQueue, placeholder, template, tick;
          placeholder = angular.element('<!-- recursive -->');
          attachQueue = [];
          tick = false;
          template = tElement.contents().clone();
          tElement.html('');
          transclude = $compile(template, function(scope, cloneAttachFn) {
            var clone;
            clone = placeholder.clone();
            cloneAttachFn(clone);
            $timeout(function() {
              transclude(scope, function(el, scope) {
                return attachQueue.push([clone, el]);
              });
              if (!tick) {
                tick = true;
                return requestAnimationFrame(function() {
                  var el, _i, _len, _ref;
                  tick = false;
                  for (_i = 0, _len = attachQueue.length; _i < _len; _i++) {
                    _ref = attachQueue[_i], clone = _ref[0], el = _ref[1];
                    clone.after(el);
                    clone.bind('$destroy', function() {
                      return el.remove();
                    });
                  }
                  return attachQueue = [];
                });
              }
            });
            return clone;
          });
          return {
            post: function(scope, iElement, iAttrs, controller) {
              return transclude(scope, function(contents) {
                return iElement.append(contents);
              });
            }
          };
        },
        restrict: 'A',
        terminal: true
      };
    }
  ];

  resettable = function() {
    return {
      compile: function(tElement, tAttrs, transclude) {
        return {
          post: function(scope, iElement, iAttrs) {
            var reset;
            reset = function() {
              return transclude(scope, function(el) {
                iElement.replaceWith(el);
                return iElement = el;
              });
            };
            reset();
            return scope.$on('$reset', reset);
          }
        };
      },
      priority: 5000,
      restrict: 'A',
      transclude: 'element'
    };
  };

  /*
  # The slow validation directive ties an to a model controller and hides
  # it while the model is being edited. This behavior improves the user
  # experience of filling out forms by delaying validation messages until
  # after the user has made a mistake.
  */


  slowValidate = [
    '$parse', '$timeout', function($parse, $timeout) {
      return {
        link: function(scope, elem, attr, ctrl) {
          var promise, _ref, _ref1;
          if (ctrl == null) {
            return;
          }
          promise = null;
          elem.addClass('slow-validate');
          return (_ref = ctrl[attr.slowValidate]) != null ? (_ref1 = _ref.$viewChangeListeners) != null ? _ref1.push(function(value) {
            elem.removeClass('slow-validate-show');
            if (promise) {
              $timeout.cancel(promise);
              promise = null;
            }
            return promise = $timeout(function() {
              return elem.addClass('slow-validate-show');
            });
          }) : void 0 : void 0;
        },
        require: '^form',
        restrict: 'A'
      };
    }
  ];

  tabReveal = [
    '$parse', function($parse) {
      return {
        compile: function(tElement, tAttrs, transclude) {
          var hiddenPanesGet, panes;
          panes = [];
          hiddenPanesGet = $parse(tAttrs.tabReveal);
          return {
            pre: function(scope, iElement, iAttrs, _arg) {
              var addPane, ngModel, tabbable, _ref,
                _this = this;
              _ref = _arg != null ? _arg : controller, ngModel = _ref[0], tabbable = _ref[1];
              addPane = tabbable.addPane;
              return tabbable.addPane = function(element, attr) {
                var removePane;
                removePane = addPane.call(tabbable, element, attr);
                panes.push({
                  element: element,
                  attr: attr
                });
                return function() {
                  var i, _i, _ref1;
                  for (i = _i = 0, _ref1 = panes.length; 0 <= _ref1 ? _i <= _ref1 : _i >= _ref1; i = 0 <= _ref1 ? ++_i : --_i) {
                    if (panes[i].element === element) {
                      panes.splice(i, 1);
                      break;
                    }
                  }
                  return removePane();
                };
              };
            },
            post: function(scope, iElement, iAttrs, _arg) {
              var ngModel, render, tabbable, tabs, _ref;
              _ref = _arg != null ? _arg : controller, ngModel = _ref[0], tabbable = _ref[1];
              tabs = angular.element(iElement.children()[0].childNodes);
              render = angular.bind(ngModel, ngModel.$render);
              return ngModel.$render = function() {
                var hiddenPanes, i, pane, value, _i, _ref1, _results;
                render();
                hiddenPanes = hiddenPanesGet(scope);
                if (!angular.isArray(hiddenPanes)) {
                  return;
                }
                _results = [];
                for (i = _i = 0, _ref1 = panes.length - 1; 0 <= _ref1 ? _i <= _ref1 : _i >= _ref1; i = 0 <= _ref1 ? ++_i : --_i) {
                  pane = panes[i];
                  value = pane.attr.value || pane.attr.title;
                  if (value === ngModel.$viewValue) {
                    pane.element.css('display', '');
                    _results.push(angular.element(tabs[i]).css('display', ''));
                  } else if (__indexOf.call(hiddenPanes, value) >= 0) {
                    pane.element.css('display', 'none');
                    _results.push(angular.element(tabs[i]).css('display', 'none'));
                  } else {
                    _results.push(void 0);
                  }
                }
                return _results;
              };
            }
          };
        },
        require: ['ngModel', 'tabbable']
      };
    }
  ];

  thread = function() {
    return {
      link: function(scope, elem, attr, ctrl) {
        var childrenEditing, sel;
        childrenEditing = {};
        sel = window.getSelection();
        scope.toggleCollapsedDown = function(event) {
          event.stopPropagation();
          return scope.oldSelection = sel.toString();
        };
        scope.toggleCollapsed = function(event) {
          event.stopPropagation();
          if (sel.toString() !== scope.oldSelection) {
            return;
          }
          if (Object.keys(childrenEditing).length !== 0) {
            return;
          }
          scope.collapsed = !scope.collapsed;
          if (!scope.collapsed) {
            return scope.openDetails(scope.annotation);
          }
        };
        return scope.$on('toggleEditing', function(event) {
          var $id, editing, _ref;
          _ref = event.targetScope, $id = _ref.$id, editing = _ref.editing;
          if (editing) {
            scope.collapsed = false;
            if (!childrenEditing[$id]) {
              event.targetScope.$on('$destroy', function() {
                return delete childrenEditing[$id];
              });
              return childrenEditing[$id] = true;
            }
          } else {
            return delete childrenEditing[$id];
          }
        });
      },
      restrict: 'C'
    };
  };

  userPicker = function() {
    return {
      restrict: 'ACE',
      scope: {
        model: '=userPickerModel',
        options: '=userPickerOptions'
      },
      templateUrl: 'userPicker.html'
    };
  };

  repeatAnim = function() {
    return {
      restrict: 'A',
      scope: {
        array: '='
      },
      template: '<div ng-init="runAnimOnLast()"><div ng-transclude></div></div>',
      transclude: true,
      controller: function($scope, $element, $attrs) {
        return $scope.runAnimOnLast = function() {
          var item, itemElm;
          item = $scope.array[0];
          itemElm = jQuery($element).children().first().children();
          if (item._anim == null) {
            return;
          }
          if (item._anim === 'fade') {
            return itemElm.css({
              opacity: 0
            }).animate({
              opacity: 1
            }, 1500);
          } else {
            if (item._anim === 'slide') {
              return itemElm.css({
                'margin-left': itemElm.width()
              }).animate({
                'margin-left': '0px'
              }, 1500);
            }
          }
        };
      }
    };
  };

  tags = [
    '$window', function($window) {
      return {
        link: function(scope, elem, attr, ctrl) {
          if (ctrl == null) {
            return;
          }
          elem.tagit({
            caseSensitive: false,
            placeholderText: attr.placeholder,
            keepPlaceholder: true,
            preprocessTag: function(val) {
              return val.replace(/[^a-zA-Z0-9\-\_\s]/g, '');
            },
            afterTagAdded: function(evt, ui) {
              return ctrl.$setViewValue(elem.tagit('assignedTags'));
            },
            afterTagRemoved: function(evt, ui) {
              return ctrl.$setViewValue(elem.tagit('assignedTags'));
            },
            autocomplete: {
              source: []
            },
            onTagClicked: function(evt, ui) {
              var tag;
              evt.stopPropagation();
              tag = ui.tagLabel;
              return $window.open("/t/" + tag);
            }
          });
          ctrl.$formatters.push(function(tags) {
            var assigned, t, _i, _j, _len, _len1;
            if (tags == null) {
              tags = [];
            }
            assigned = elem.tagit('assignedTags');
            for (_i = 0, _len = assigned.length; _i < _len; _i++) {
              t = assigned[_i];
              if (__indexOf.call(tags, t) < 0) {
                elem.tagit('removeTagByLabel', t);
              }
            }
            for (_j = 0, _len1 = tags.length; _j < _len1; _j++) {
              t = tags[_j];
              if (__indexOf.call(assigned, t) < 0) {
                elem.tagit('createTag', t);
              }
            }
            if (assigned.length || !attr.readOnly) {
              return elem.show();
            } else {
              return elem.hide();
            }
          });
          return attr.$observe('readonly', function(readonly) {
            var assigned, tagInput;
            tagInput = elem.find('input').last();
            assigned = elem.tagit('assignedTags');
            if (readonly) {
              tagInput.attr('disabled', true);
              tagInput.removeAttr('placeholder');
              if (assigned.length) {
                return elem.show();
              } else {
                return elem.hide();
              }
            } else {
              tagInput.removeAttr('disabled');
              tagInput.attr('placeholder', attr['placeholder']);
              return elem.show();
            }
          });
        },
        require: '?ngModel',
        restrict: 'C'
      };
    }
  ];

  notification = [
    '$filter', function($filter) {
      return {
        link: function(scope, elem, attrs, controller) {
          if (controller == null) {
            return;
          }
          return scope.model = controller;
        },
        controller: 'NotificationController',
        priority: 100,
        require: '?ngModel',
        restrict: 'C',
        scope: {},
        templateUrl: 'notification.html'
      };
    }
  ];

  username = [
    '$filter', '$window', function($filter, $window) {
      return {
        link: function(scope, elem, attr, ctrl) {
          if (ctrl == null) {
            return;
          }
          ctrl.$render = function() {
            return scope.uname = ($filter('userName'))(ctrl.$viewValue);
          };
          return scope.uclick = function(event) {
            event.stopPropagation();
            return $window.open("/u/" + scope.uname);
          };
        },
        require: '?ngModel',
        restrict: 'E',
        template: '<span class="user" ng-click="uclick($event)">{{uname}}</span>'
      };
    }
  ];

  fuzzytime = [
    '$filter', '$window', function($filter, $window) {
      return {
        link: function(scope, elem, attr, ctrl) {
          var timefunct;
          if (ctrl == null) {
            return;
          }
          ctrl.$render = function() {
            return scope.ftime = ($filter('fuzzyTime'))(ctrl.$viewValue);
          };
          timefunct = function() {
            var _this = this;
            return $window.setInterval(function() {
              scope.ftime = ($filter('fuzzyTime'))(ctrl.$viewValue);
              return scope.$digest();
            }, 5000);
          };
          scope.timer = timefunct();
          return scope.$on('$destroy', function() {
            return $window.clearInterval(scope.timer);
          });
        },
        require: '?ngModel',
        restrict: 'E',
        template: '<span class="small">{{ftime | date:mediumDate}}</span>'
      };
    }
  ];

  streamviewer = [
    function() {
      return {
        link: function(scope, elem, attr, ctrl) {
          if (ctrl == null) {

          }
        },
        require: '?ngModel',
        restrict: 'E',
        templateUrl: 'streamviewer.html'
      };
    }
  ];

  whenscrolled = [
    '$window', function($window) {
      return {
        link: function(scope, elem, attr) {
          $window = angular.element($window);
          return $window.on('scroll', function() {
            var elementBottom, remaining, shouldScroll, windowBottom;
            windowBottom = $window.height() + $window.scrollTop();
            elementBottom = elem.offset().top + elem.height();
            remaining = elementBottom - windowBottom;
            shouldScroll = remaining <= $window.height() * 0;
            if (shouldScroll) {
              return scope.$apply(attr.whenscrolled);
            }
          });
        }
      };
    }
  ];

  angular.module('h.directives', ['ngSanitize']).directive('authentication', authentication).directive('fuzzytime', fuzzytime).directive('markdown', markdown).directive('privacy', privacy).directive('recursive', recursive).directive('resettable', resettable).directive('slowValidate', slowValidate).directive('tabReveal', tabReveal).directive('tags', tags).directive('thread', thread).directive('username', username).directive('userPicker', userPicker).directive('repeatAnim', repeatAnim).directive('notification', notification).directive('streamviewer', streamviewer).directive('whenscrolled', whenscrolled);

}).call(this);
