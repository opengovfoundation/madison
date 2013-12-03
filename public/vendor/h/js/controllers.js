(function() {
  var Annotation, App, Editor, Notification, Search, Viewer,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; },
    __slice = [].slice;

  App = (function() {
    App.prototype.scope = {
      frame: {
        visible: false
      },
      sheet: {
        collapsed: true,
        tab: null
      },
      ongoingHighlightSwitch: false
    };

    App.$inject = ['$element', '$filter', '$http', '$location', '$rootScope', '$scope', '$timeout', 'annotator', 'authentication', 'streamfilter'];

    function App($element, $filter, $http, $location, $rootScope, $scope, $timeout, annotator, authentication, streamfilter) {
      var baseUrl, host, plugins, providers, search_query, _ref,
        _this = this;
      baseUrl = (_ref = angular.element('head base')[0]) != null ? _ref.href : void 0;
      if (baseUrl == null) {
        baseUrl = $location.absUrl().replace($location.url(), '');
      }
      baseUrl = baseUrl.replace(/#$/, '');
      baseUrl = baseUrl.replace(/\/*$/, '/');
      $scope.baseUrl = baseUrl;
      plugins = annotator.plugins, host = annotator.host, providers = annotator.providers;
      $scope.$watch('auth.personas', function(newValue, oldValue) {
        if (!(newValue != null ? newValue.length : void 0)) {
          authentication.persona = null;
          authentication.token = null;
          if (annotator.tool === 'highlight') {
            annotator.setTool('comment');
            return $scope.skipAuthChangeReload = true;
          }
        }
      });
      $scope.$watch('auth.persona', function(newValue, oldValue) {
        if ((oldValue != null) && (newValue == null)) {
          if (annotator.discardDrafts()) {
            return authentication.$logout(function() {
              return $scope.$broadcast('$reset');
            });
          } else {
            return $scope.auth.persona = oldValue;
          }
        } else if (newValue != null) {
          return $scope.sheet.collapsed = true;
        }
      });
      $scope.$watch('auth.token', function(newValue, oldValue) {
        if (plugins.Auth != null) {
          plugins.Auth.token = newValue;
          plugins.Auth.updateHeaders();
        }
        if (newValue != null) {
          if (plugins.Auth == null) {
            annotator.addPlugin('Auth', {
              tokenUrl: $scope.tokenUrl,
              token: newValue
            });
          } else {
            plugins.Auth.setToken(newValue);
          }
          plugins.Auth.withToken(function(token) {
            plugins.Permissions._setAuthFromToken(token);
            if (annotator.ongoing_edit) {
              $timeout(function() {
                return annotator.clickAdder();
              }, 1000);
            }
            if ($scope.ongoingHighlightSwitch) {
              $scope.ongoingHighlightSwitch = false;
              return annotator.setTool('highlight');
            }
          });
        } else {
          plugins.Permissions.setUser(null);
          delete plugins.Auth;
        }
        if (newValue !== oldValue) {
          if (!$scope.skipAuthChangeReload) {
            $scope.reloadAnnotations();
          }
          return delete $scope.skipAuthChangeReload;
        }
      });
      $scope.$watch('socialView.name', function(newValue, oldValue) {
        if (newValue === oldValue) {
          return;
        }
        console.log("Social View changed to '" + newValue + "'. Reloading annotations.");
        return $scope.reloadAnnotations();
      });
      $scope.$watch('frame.visible', function(newValue, oldValue) {
        var p, routeName, _i, _len, _ref1, _results;
        routeName = $location.path().replace(/^\//, '');
        if (newValue) {
          annotator.show();
          return annotator.host.notify({
            method: 'showFrame',
            params: routeName
          });
        } else if (oldValue) {
          $scope.sheet.collapsed = true;
          annotator.hide();
          annotator.host.notify({
            method: 'hideFrame',
            params: routeName
          });
          _ref1 = annotator.providers;
          _results = [];
          for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
            p = _ref1[_i];
            _results.push(p.channel.notify({
              method: 'setActiveHighlights'
            }));
          }
          return _results;
        }
      });
      $scope.$watch('sheet.collapsed', function(hidden) {
        if (!hidden) {
          return $scope.sheet.tab = 'login';
        }
      });
      $scope.$watch('sheet.tab', function(tab) {
        var reset, unwatch,
          _this = this;
        if (!tab) {
          return;
        }
        $timeout(function() {
          return $element.find('form').filter(function() {
            return this.name === tab;
          }).find('input').filter(function() {
            return this.type !== 'hidden';
          }).first().focus();
        }, 10);
        reset = $timeout((function() {
          return $scope.$broadcast('$reset');
        }), 60000);
        return unwatch = $scope.$watch('sheet.tab', function(newTab) {
          $timeout.cancel(reset);
          if (newTab) {
            return reset = $timeout((function() {
              return $scope.$broadcast('$reset');
            }), 60000);
          } else {
            $scope.ongoingHighlightSwitch = false;
            annotator.ongoing_edit = null;
            return unwatch();
          }
        });
      });
      $scope.$on('back', function() {
        var _ref1;
        if (!annotator.discardDrafts()) {
          return;
        }
        if ($location.path() === '/viewer' && (((_ref1 = $location.search()) != null ? _ref1.id : void 0) != null)) {
          return $location.search('id', null).replace();
        } else {
          return annotator.hide();
        }
      });
      $scope.$on('showAuth', function(event, show) {
        if (show == null) {
          show = true;
        }
        return angular.extend($scope.sheet, {
          collapsed: !show,
          tab: 'login'
        });
      });
      $scope.$on('$reset', function() {
        var base;
        annotator.ongoing_edit = null;
        base = angular.copy(_this.scope);
        return angular.extend($scope, base, {
          auth: authentication,
          frame: $scope.frame || _this.scope.frame,
          socialView: annotator.socialView
        });
      });
      $scope.$on('success', function(event, action) {
        if (action === 'claim') {
          return $scope.sheet.tab = 'activate';
        }
      });
      $scope.$broadcast('$reset');
      $scope.leaveSearch = function() {
        var p, _i, _len, _ref1, _results;
        $scope.show_search = false;
        _this.visualSearch.searchBox.disableFacets();
        _this.visualSearch.searchBox.value('');
        _this.visualSearch.searchBox.flags.allSelected = false;
        _ref1 = annotator.providers;
        _results = [];
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          p = _ref1[_i];
          _results.push(p.channel.notify({
            method: 'setDynamicBucketMode',
            params: true
          }));
        }
        return _results;
      };
      $scope.$on('$routeChangeStart', function(current, next) {
        var willSearch, _ref1;
        if (next.$$route == null) {
          return;
        }
        willSearch = ((_ref1 = next.$$route) != null ? _ref1.controller : void 0) === "SearchController";
        if ($scope.inSearch && !willSearch) {
          $scope.leaveSearch();
        }
        return $scope.inSearch = willSearch;
      });
      $timeout(function() {
        var $i, i, _i, _len, _ref1, _results;
        _ref1 = $element.find('input');
        _results = [];
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          i = _ref1[_i];
          if (!i.value) {
            continue;
          }
          $i = angular.element(i);
          $i.triggerHandler('change');
          _results.push($i.triggerHandler('input'));
        }
        return _results;
      }, 200);
      this.user_filter = $filter('userName');
      search_query = '';
      this.visualSearch = VS.init({
        container: $element.find('.visual-search'),
        query: search_query,
        callbacks: {
          search: function(query, searchCollection) {
            var annotation, annotations, category, delta, found, in_body_text, matched, matches, p, priv_public, quote_search, search, searchItem, tag, tag_search, target, text_tokens, token, userName, value, whole_document, _i, _j, _k, _l, _len, _len1, _len2, _len3, _len4, _len5, _len6, _m, _n, _o, _ref1, _ref2, _ref3, _ref4, _ref5;
            if (!query) {
              if ($scope.inSearch) {
                $location.path('/viewer');
                $rootScope.$digest();
              }
              return;
            }
            matched = [];
            whole_document = true;
            in_body_text = '';
            _ref1 = searchCollection.models;
            for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
              searchItem = _ref1[_i];
              if (searchItem.attributes.category === 'scope' && searchItem.attributes.value === 'sidebar') {
                whole_document = false;
              }
              if (searchItem.attributes.category === 'text') {
                in_body_text = searchItem.attributes.value.toLowerCase();
                text_tokens = searchItem.attributes.value.split(' ');
              }
              if (searchItem.attributes.category === 'tag') {
                tag_search = searchItem.attributes.value.toLowerCase();
              }
              if (searchItem.attributes.category === 'quote') {
                quote_search = searchItem.attributes.value.toLowerCase();
              }
            }
            if (whole_document) {
              annotations = annotator.plugins.Store.annotations;
            } else {
              annotations = $rootScope.annotations;
            }
            for (_j = 0, _len1 = annotations.length; _j < _len1; _j++) {
              annotation = annotations[_j];
              matches = true;
              _ref2 = searchCollection.models;
              for (_k = 0, _len2 = _ref2.length; _k < _len2; _k++) {
                searchItem = _ref2[_k];
                category = searchItem.attributes.category;
                value = searchItem.attributes.value;
                switch (category) {
                  case 'user':
                    userName = _this.user_filter(annotation.user);
                    if (userName.toLowerCase() !== value.toLowerCase()) {
                      matches = false;
                      break;
                    }
                    break;
                  case 'text':
                    if (annotation.text == null) {
                      matches = false;
                      break;
                    }
                    for (_l = 0, _len3 = text_tokens.length; _l < _len3; _l++) {
                      token = text_tokens[_l];
                      if (!(annotation.text.toLowerCase().indexOf(token.toLowerCase()) > -1)) {
                        matches = false;
                        break;
                      }
                    }
                    break;
                  case 'quote':
                    if (annotation.references != null) {
                      matches = false;
                      break;
                    } else {
                      found = false;
                      _ref3 = annotation.target;
                      for (_m = 0, _len4 = _ref3.length; _m < _len4; _m++) {
                        target = _ref3[_m];
                        if ((target.quote != null) && target.quote.toLowerCase().indexOf(quote_search) > -1) {
                          found = true;
                          break;
                        }
                      }
                      if (!found) {
                        matches = false;
                        break;
                      }
                    }
                    break;
                  case 'tag':
                    if (annotation.tags == null) {
                      matches = false;
                      break;
                    }
                    found = false;
                    _ref4 = annotation.tags;
                    for (_n = 0, _len5 = _ref4.length; _n < _len5; _n++) {
                      tag = _ref4[_n];
                      if (tag.toLowerCase().indexOf(tag_search) > -1) {
                        found = true;
                        break;
                      }
                    }
                    if (!found) {
                      matches = false;
                    }
                    break;
                  case 'time':
                    delta = Math.round((+(new Date) - new Date(annotation.updated)) / 1000);
                    switch (value) {
                      case '5 min':
                        if (!(delta <= 60 * 5)) {
                          matches = false;
                        }
                        break;
                      case '30 min':
                        if (!(delta <= 60 * 30)) {
                          matches = false;
                        }
                        break;
                      case '1 hour':
                        if (!(delta <= 60 * 60)) {
                          matches = false;
                        }
                        break;
                      case '12 hours':
                        if (!(delta <= 60 * 60 * 12)) {
                          matches = false;
                        }
                        break;
                      case '1 day':
                        if (!(delta <= 60 * 60 * 24)) {
                          matches = false;
                        }
                        break;
                      case '1 week':
                        if (!(delta <= 60 * 60 * 24 * 7)) {
                          matches = false;
                        }
                        break;
                      case '1 month':
                        if (!(delta <= 60 * 60 * 24 * 31)) {
                          matches = false;
                        }
                        break;
                      case '1 year':
                        if (!(delta <= 60 * 60 * 24 * 366)) {
                          matches = false;
                        }
                    }
                    break;
                  case 'group':
                    priv_public = __indexOf.call(annotation.permissions.read || [], 'group:__world__') >= 0;
                    switch (value) {
                      case 'Public':
                        if (!priv_public) {
                          matches = false;
                        }
                        break;
                      case 'Private':
                        if (priv_public) {
                          matches = false;
                        }
                    }
                }
              }
              if (matches) {
                matched.push(annotation.id);
              }
            }
            search = {
              whole_document: whole_document,
              matched: matched,
              in_body_text: in_body_text,
              quote: quote_search
            };
            $location.path('/page_search').search(search);
            if (!$scope.inSearch) {
              _ref5 = annotator.providers;
              for (_o = 0, _len6 = _ref5.length; _o < _len6; _o++) {
                p = _ref5[_o];
                p.channel.notify({
                  method: 'setDynamicBucketMode',
                  params: false
                });
              }
            }
            return $rootScope.$digest();
          },
          facetMatches: function(callback) {
            if ($scope.show_search) {
              return callback(['text', 'tag', 'quote', 'scope', 'group', 'time', 'user'], {
                preserveOrder: true
              });
            }
          },
          valueMatches: function(facet, searchTerm, callback) {
            switch (facet) {
              case 'group':
                return callback(['Public', 'Private']);
              case 'scope':
                return callback(['sidebar', 'document']);
              case 'time':
                return callback(['5 min', '30 min', '1 hour', '12 hours', '1 day', '1 week', '1 month', '1 year'], {
                  preserveOrder: true
                });
            }
          },
          clearSearch: function(original) {
            original();
            if (!$scope.inSearch) {
              $scope.leaveSearch();
            }
            $location.path('/viewer');
            return $rootScope.$digest();
          }
        }
      });
      if (search_query.length > 0) {
        $timeout(function() {
          return _this.visualSearch.searchBox.searchEvent('');
        }, 1500);
      }
      $scope.reloadAnnotations = function() {
        var Store, a, annotations, _i, _len;
        if (!annotator.plugins.Store) {
          return;
        }
        $scope.new_updates = 0;
        $scope.$root.annotations = [];
        annotator.threading.thread([]);
        Store = annotator.plugins.Store;
        annotations = Store.annotations;
        annotator.plugins.Store.annotations = [];
        for (_i = 0, _len = annotations.length; _i < _len; _i++) {
          a = annotations[_i];
          annotator.deleteAnnotation(a);
        }
        Store.annotator = {
          loadAnnotations: angular.noop
        };
        Store._apiRequest = angular.noop;
        Store.updateAnnotation = angular.noop;
        delete annotator.plugins.Store;
        return annotator.addPlugin('Store', annotator.options.Store);
      };
      $scope.notifications = [];
      $scope.removeNotificationUpdate = function() {
        var index, notif, _i, _len, _ref1;
        index = -1;
        _ref1 = $scope.notifications;
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          notif = _ref1[_i];
          if (notif.type === 'update') {
            index = $scope.notifications.indexOf(notif);
            break;
          }
        }
        if (index > -1) {
          return $scope.notifications.splice(index, 1);
        }
      };
      $scope.addUpdateNotification = function() {
        var notification, text,
          _this = this;
        if (!($scope.new_updates > 0)) {
          if ($scope.new_updates < 2) {
            text = 'change.';
          } else {
            text = 'changes.';
          }
          notification = {
            type: 'update',
            text: 'Click to load ' + $scope.new_updates + ' ' + text,
            callback: function() {
              $scope.reloadAnnotations();
              return $scope.removeNotificationUpdate();
            },
            close: $scope.removeNotificationUpdate
          };
          return $scope.notifications.unshift(notification);
        }
      };
      $scope.$watch('new_updates', function(updates, oldUpdates) {
        var notif, p, text, _i, _j, _len, _len1, _ref1, _ref2, _results;
        if (!(updates || oldUpdates)) {
          return;
        }
        _ref1 = $scope.notifications;
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          notif = _ref1[_i];
          if (notif.type === 'update') {
            if ($scope.new_updates < 2) {
              text = 'change.';
            } else {
              text = 'changes.';
            }
            notif.text = 'Click to load ' + updates + ' ' + text;
          }
        }
        _ref2 = annotator.providers;
        _results = [];
        for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
          p = _ref2[_j];
          _results.push(p.channel.notify({
            method: 'updateNotificationCounter',
            params: updates
          }));
        }
        return _results;
      });
      $scope.$watch('show_search', function(value, old) {
        if (value && !old) {
          return $timeout(function() {
            return $element.find('.visual-search').find('input').last().focus();
          }, 10);
        }
      });
      $scope.initUpdater = function() {
        var e, filter, path, uris,
          _this = this;
        $scope.new_updates = 0;
        path = $scope.baseUrl.replace(/\/\w+\/$/, '/');
        path = "" + path + "__streamer__";
        uris = ((function() {
          var _results;
          _results = [];
          for (e in annotator.plugins.Store.entities) {
            _results.push(e);
          }
          return _results;
        })()).join(',');
        filter = streamfilter.setPastDataNone().setMatchPolicyIncludeAny().setClausesParse('uri:[' + uris).getFilter();
        $scope.updater = new SockJS(path);
        $scope.updater.onopen = function() {
          var sockmsg;
          sockmsg = {
            filter: filter,
            clientID: annotator.clientID
          };
          return $scope.updater.send(JSON.stringify(sockmsg));
        };
        $scope.updater.onclose = function() {
          return $timeout($scope.initUpdater, 60000);
        };
        return $scope.updater.onmessage = function(msg) {
          var action, clientID, data, p, user;
          if (!((msg.data.type != null) && msg.data.type === 'annotation-notification')) {
            return;
          }
          data = msg.data.payload;
          action = msg.data.options.action;
          clientID = msg.data.options.clientID;
          if (clientID === annotator.clientID) {
            return;
          }
          if (!(data instanceof Array)) {
            data = [data];
          }
          p = $scope.auth.persona;
          user = p != null ? "acct:" + p.username + "@" + p.provider : '';
          if (!(data instanceof Array)) {
            data = [data];
          }
          return $scope.$apply(function() {
            var d, _i, _len, _results;
            if ($scope.socialView.name === 'single-player') {
              _results = [];
              for (_i = 0, _len = data.length; _i < _len; _i++) {
                d = data[_i];
                if (d.user === user) {
                  $scope.addUpdateNotification();
                  $scope.new_updates += 1;
                  break;
                } else {
                  _results.push(void 0);
                }
              }
              return _results;
            } else {
              if (data.length > 0) {
                $scope.addUpdateNotification();
                return $scope.new_updates += 1;
              }
            }
          });
        };
      };
      $timeout(function() {
        return $scope.initUpdater();
      }, 5000);
    }

    return App;

  })();

  Annotation = (function() {
    Annotation.$inject = ['$element', '$location', '$scope', 'annotator', 'drafts', '$timeout', '$window'];

    function Annotation($element, $location, $scope, annotator, drafts, $timeout, $window) {
      var threading;
      threading = annotator.threading;
      $scope.action = 'create';
      $scope.editing = false;
      $scope.cancel = function($event) {
        if ($event != null) {
          $event.stopPropagation();
        }
        $scope.editing = false;
        drafts.remove($scope.model.$modelValue);
        switch ($scope.action) {
          case 'create':
            return annotator.deleteAnnotation($scope.model.$modelValue);
          default:
            $scope.model.$modelValue.text = $scope.origText;
            $scope.model.$modelValue.tags = $scope.origTags;
            return $scope.action = 'create';
        }
      };
      $scope.save = function($event) {
        var a, annotation, root, _ref, _ref1;
        if ($event != null) {
          $event.stopPropagation();
        }
        annotation = $scope.model.$modelValue;
        if (annotator.isComment(annotation) && !annotation.text && !((_ref = annotation.tags) != null ? _ref.length : void 0)) {
          $window.alert("You can not add a comment without adding some text, or at least a tag.");
          return;
        }
        if ($scope.form.privacy.$viewValue === "Public" && !annotation.text && !((_ref1 = annotation.tags) != null ? _ref1.length : void 0)) {
          $window.alert("You can not make this annotation public without adding some text, or at least a tag.");
          return;
        }
        $scope.rebuildHighlightText();
        $scope.editing = false;
        drafts.remove(annotation);
        switch ($scope.action) {
          case 'create':
            return annotator.publish('annotationCreated', annotation);
          case 'delete':
            root = $scope.$root.annotations;
            root = (function() {
              var _i, _len, _results;
              _results = [];
              for (_i = 0, _len = root.length; _i < _len; _i++) {
                a = root[_i];
                if (a !== root) {
                  _results.push(a);
                }
              }
              return _results;
            })();
            annotation.deleted = true;
            if (annotation.text == null) {
              annotation.text = '';
            }
            return annotator.updateAnnotation(annotation);
          default:
            return annotator.updateAnnotation(annotation);
        }
      };
      $scope.reply = function($event) {
        var references, reply;
        if ($event != null) {
          $event.stopPropagation();
        }
        if (!((annotator.plugins.Auth != null) && annotator.plugins.Auth.haveValidToken())) {
          $window.alert("In order to reply, you need to sign in.");
          return;
        }
        references = $scope.thread.message.references ? __slice.call($scope.thread.message.references).concat([$scope.thread.message.id]) : [$scope.thread.message.id];
        reply = {
          references: references,
          uri: $scope.thread.message.uri
        };
        annotator.publish('beforeAnnotationCreated', [reply]);
        return drafts.add(reply);
      };
      $scope.edit = function($event) {
        if ($event != null) {
          $event.stopPropagation();
        }
        $scope.action = 'edit';
        $scope.editing = true;
        $scope.origText = $scope.model.$modelValue.text;
        $scope.origTags = $scope.model.$modelValue.tags;
        return drafts.add($scope.model.$modelValue, function() {
          return $scope.cancel();
        });
      };
      $scope["delete"] = function($event) {
        var annotation, replies, reply, _i, _len, _ref, _ref1, _ref2;
        if ($event != null) {
          $event.stopPropagation();
        }
        annotation = $scope.model.$modelValue;
        replies = ((_ref = $scope.thread.children) != null ? _ref.length : void 0) || 0;
        if (replies === 0 || $scope.form.privacy.$viewValue === 'Private') {
          if (confirm("Are you sure you want to delete this annotation?")) {
            if ($scope.form.privacy.$viewValue === 'Private' && replies) {
              _ref1 = $scope.thread.flattenChildren();
              for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
                reply = _ref1[_i];
                if ((((_ref2 = annotator.plugins) != null ? _ref2.Permissions : void 0) != null) && annotator.plugins.Permissions.authorize('delete', reply)) {
                  annotator.deleteAnnotation(reply);
                }
              }
            }
            return annotator.deleteAnnotation(annotation);
          }
        } else {
          $scope.action = 'delete';
          $scope.editing = true;
          $scope.origText = $scope.model.$modelValue.text;
          $scope.origTags = $scope.model.$modelValue.tags;
          $scope.model.$modelValue.text = '';
          return $scope.model.$modelValue.tags = '';
        }
      };
      $scope.$watch('editing', function() {
        return $scope.$emit('toggleEditing');
      });
      $scope.$watch('model.$modelValue.id', function(id) {
        var annotation;
        if (id != null) {
          annotation = $scope.model.$modelValue;
          $scope.thread = annotation.thread;
          if ((annotation != null) && drafts.contains(annotation)) {
            return $scope.editing = true;
          }
        }
      });
      $scope.$watch('shared', function(newValue) {
        var prefix;
        if ((newValue != null) === true) {
          $timeout(function() {
            return $element.find('input').focus();
          });
          $timeout(function() {
            return $element.find('input').select();
          });
          prefix = $scope.$parent.baseUrl.replace(/\/\w+\/$/, '');
          $scope.shared_link = prefix + '/a/' + $scope.model.$modelValue.id;
          return $scope.shared = false;
        }
      });
      $scope.$watchCollection('model.$modelValue.thread.children', function(newValue) {
        var annotation, r, replies;
        if (newValue == null) {
          newValue = [];
        }
        annotation = $scope.model.$modelValue;
        if (!annotation) {
          return;
        }
        replies = (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = newValue.length; _i < _len; _i++) {
            r = newValue[_i];
            _results.push(r.message);
          }
          return _results;
        })();
        replies = replies.sort(annotator.sortAnnotations).reverse();
        return annotation.reply_list = replies;
      });
      $scope.toggle = function() {
        return $element.find('.share-dialog').slideToggle();
      };
      $scope.share = function($event) {
        $event.stopPropagation();
        if ($element.find('.share-dialog').is(":visible")) {
          return;
        }
        $scope.shared = !$scope.shared;
        return $scope.toggle();
      };
      $scope.rebuildHighlightText = function() {
        var regexp, _i, _len, _ref, _results;
        if (annotator.text_regexp != null) {
          $scope.model.$modelValue.highlightText = $scope.model.$modelValue.text;
          _ref = annotator.text_regexp;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            regexp = _ref[_i];
            _results.push($scope.model.$modelValue.highlightText = $scope.model.$modelValue.highlightText.replace(regexp, annotator.highlighter));
          }
          return _results;
        }
      };
    }

    return Annotation;

  })();

  Editor = (function() {
    Editor.$inject = ['$location', '$routeParams', '$scope', 'annotator'];

    function Editor($location, $routeParams, $scope, annotator) {
      var cancel, providers, save;
      providers = annotator.providers;
      save = function() {
        var p, _i, _len, _results;
        $location.path('/viewer').search('id', $scope.annotation.id).replace();
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          p.channel.notify({
            method: 'onEditorSubmit'
          });
          _results.push(p.channel.notify({
            method: 'onEditorHide'
          }));
        }
        return _results;
      };
      cancel = function() {
        var p, _i, _len, _results;
        $location.path('/viewer').search('id', null).replace();
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          _results.push(p.channel.notify({
            method: 'onEditorHide'
          }));
        }
        return _results;
      };
      $scope.action = $routeParams.action != null ? $routeParams.action : 'create';
      if ($scope.action === 'create') {
        annotator.subscribe('annotationCreated', save);
        annotator.subscribe('annotationDeleted', cancel);
      } else {
        if ($scope.action === 'edit' || $scope.action === 'redact') {
          annotator.subscribe('annotationUpdated', save);
        }
      }
      $scope.$on('$destroy', function() {
        if ($scope.action === 'edit' || $scope.action === 'redact') {
          return annotator.unsubscribe('annotationUpdated', save);
        } else {
          if ($scope.action === 'create') {
            annotator.unsubscribe('annotationCreated', save);
            return annotator.unsubscribe('annotationDeleted', cancel);
          }
        }
      });
      $scope.annotation = annotator.ongoing_edit;
      annotator.ongoing_edit = null;
    }

    return Editor;

  })();

  Viewer = (function() {
    Viewer.$inject = ['$location', '$rootScope', '$routeParams', '$scope', 'annotator'];

    function Viewer($location, $rootScope, $routeParams, $scope, annotator) {
      var providers, threading;
      providers = annotator.providers, threading = annotator.threading;
      $scope.focus = function(annotation) {
        var a, highlights, p, _i, _len, _results;
        if (angular.isArray(annotation)) {
          highlights = (function() {
            var _i, _len, _results;
            _results = [];
            for (_i = 0, _len = annotation.length; _i < _len; _i++) {
              a = annotation[_i];
              if (a != null) {
                _results.push(a.$$tag);
              }
            }
            return _results;
          })();
        } else if (angular.isObject(annotation)) {
          highlights = [annotation.$$tag];
        } else {
          highlights = [];
        }
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          _results.push(p.channel.notify({
            method: 'setActiveHighlights',
            params: highlights
          }));
        }
        return _results;
      };
      $scope.openDetails = function(annotation) {
        var p, _i, _len, _results;
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          _results.push(p.channel.notify({
            method: 'scrollTo',
            params: annotation.$$tag
          }));
        }
        return _results;
      };
    }

    return Viewer;

  })();

  Search = (function() {
    Search.$inject = ['$filter', '$location', '$rootScope', '$routeParams', '$scope', 'annotator'];

    function Search($filter, $location, $rootScope, $routeParams, $scope, annotator) {
      var buildRenderOrder, providers, refresh, setMoreBottom, setMoreTop, threading,
        _this = this;
      providers = annotator.providers, threading = annotator.threading;
      $scope.highlighter = '<span class="search-hl-active">$&</span>';
      $scope.filter_orderBy = $filter('orderBy');
      $scope.render_order = {};
      $scope.render_pos = {};
      $scope.ann_info = {
        shown: {},
        show_quote: {},
        more_top: {},
        more_bottom: {},
        more_top_num: {},
        more_bottom_num: {}
      };
      buildRenderOrder = function(threadid, threads) {
        var sorted, thread, _i, _len, _results;
        if (!(threads != null ? threads.length : void 0)) {
          return;
        }
        sorted = $scope.filter_orderBy(threads, $scope.sortThread, true);
        _results = [];
        for (_i = 0, _len = sorted.length; _i < _len; _i++) {
          thread = sorted[_i];
          $scope.render_pos[thread.message.id] = $scope.render_order[threadid].length;
          $scope.render_order[threadid].push(thread.message.id);
          _results.push(buildRenderOrder(threadid, thread.children));
        }
        return _results;
      };
      setMoreTop = function(threadid, annotation) {
        var pos, prev, result, _ref;
        if (_ref = annotation.id, __indexOf.call($scope.search_filter, _ref) < 0) {
          return false;
        }
        result = false;
        pos = $scope.render_pos[annotation.id];
        if (pos > 0) {
          prev = $scope.render_order[threadid][pos - 1];
          if (__indexOf.call($scope.search_filter, prev) < 0) {
            result = true;
          }
        }
        return result;
      };
      setMoreBottom = function(threadid, annotation) {
        var next, pos, result, _ref;
        if (_ref = annotation.id, __indexOf.call($scope.search_filter, _ref) < 0) {
          return false;
        }
        result = false;
        pos = $scope.render_pos[annotation.id];
        if (pos < $scope.render_order[threadid].length - 1) {
          next = $scope.render_order[threadid][pos + 1];
          if (__indexOf.call($scope.search_filter, next) < 0) {
            result = true;
          }
        }
        return result;
      };
      $scope.focus = function(annotation) {
        var a, highlights, p, _i, _len, _results;
        if (angular.isArray(annotation)) {
          highlights = (function() {
            var _i, _len, _results;
            _results = [];
            for (_i = 0, _len = annotation.length; _i < _len; _i++) {
              a = annotation[_i];
              if (a != null) {
                _results.push(a.$$tag);
              }
            }
            return _results;
          })();
        } else if (angular.isObject(annotation)) {
          highlights = [annotation.$$tag];
        } else {
          highlights = [];
        }
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          _results.push(p.channel.notify({
            method: 'setActiveHighlights',
            params: highlights
          }));
        }
        return _results;
      };
      $scope.openDetails = function(annotation) {
        var p, _i, _len, _results;
        if (!annotation) {
          return;
        }
        _results = [];
        for (_i = 0, _len = providers.length; _i < _len; _i++) {
          p = providers[_i];
          _results.push(p.channel.notify({
            method: 'scrollTo',
            params: annotation.$$tag
          }));
        }
        return _results;
      };
      refresh = function() {
        var annotation, annotation_root, child, children, has_search_result, hidden, id, last_shown, order, reference, regexp, roots, target, thread, threadid, threads, token, top_match, top_thread, _i, _j, _k, _l, _len, _len1, _len2, _len3, _len4, _len5, _len6, _len7, _len8, _len9, _m, _n, _o, _p, _q, _r, _ref, _ref1, _ref10, _ref11, _ref12, _ref2, _ref3, _ref4, _ref5, _ref6, _ref7, _ref8, _ref9;
        $scope.search_filter = $routeParams.matched;
        $scope.text_tokens = $routeParams.in_body_text.split(' ');
        $scope.text_regexp = [];
        $scope.quote = $routeParams.quote;
        $scope.quote_regexp = new RegExp($scope.quote, "ig");
        _ref = $scope.text_tokens;
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          token = _ref[_i];
          regexp = new RegExp(token, "ig");
          $scope.text_regexp.push(regexp);
        }
        annotator.text_regexp = $scope.text_regexp;
        annotator.highlighter = $scope.highlighter;
        threads = [];
        roots = {};
        $scope.render_order = {};
        _ref1 = annotator.threading.idTable;
        for (id in _ref1) {
          thread = _ref1[id];
          if (!(thread.message != null)) {
            continue;
          }
          annotation = thread.message;
          annotation_root = annotation.references != null ? annotation.references[0] : annotation.id;
          if (roots[annotation_root] != null) {
            continue;
          }
          if (_ref2 = annotation.id, __indexOf.call($scope.search_filter, _ref2) >= 0) {
            top_match = null;
            if (annotation.references != null) {
              _ref3 = annotation.references;
              for (_j = 0, _len1 = _ref3.length; _j < _len1; _j++) {
                reference = _ref3[_j];
                if (__indexOf.call($scope.search_filter, reference) >= 0) {
                  top_thread = annotator.threading.getContainer(reference);
                  top_match = top_thread.message;
                  break;
                }
              }
            }
            if (top_match) {
              threads.push(top_thread);
              $scope.render_order[top_match.id] = [];
              buildRenderOrder(top_match.id, [top_thread]);
            } else {
              threads.push(thread);
              $scope.render_order[annotation.id] = [];
              buildRenderOrder(annotation.id, [thread]);
            }
            roots[annotation_root] = true;
            continue;
          }
          children = thread.flattenChildren();
          has_search_result = false;
          if (children != null) {
            for (_k = 0, _len2 = children.length; _k < _len2; _k++) {
              child = children[_k];
              if (_ref4 = child.id, __indexOf.call($scope.search_filter, _ref4) >= 0) {
                has_search_result = true;
                break;
              }
            }
          }
          if (has_search_result) {
            threads.push(thread);
            $scope.render_order[annotation.id] = [];
            buildRenderOrder(annotation.id, [thread]);
            roots[annotation_root] = true;
          }
        }
        for (_l = 0, _len3 = threads.length; _l < _len3; _l++) {
          thread = threads[_l];
          thread.message.highlightText = thread.message.text;
          if (_ref5 = thread.message.id, __indexOf.call($scope.search_filter, _ref5) >= 0) {
            $scope.ann_info.shown[thread.message.id] = true;
            if (thread.message.text != null) {
              _ref6 = $scope.text_regexp;
              for (_m = 0, _len4 = _ref6.length; _m < _len4; _m++) {
                regexp = _ref6[_m];
                thread.message.highlightText = thread.message.highlightText.replace(regexp, $scope.highlighter);
              }
            }
          } else {
            $scope.ann_info.shown[thread.message.id] = false;
          }
          $scope.ann_info.more_top[thread.message.id] = setMoreTop(thread.message.id, thread.message);
          $scope.ann_info.more_bottom[thread.message.id] = setMoreBottom(thread.message.id, thread.message);
          if (((_ref7 = $scope.quote) != null ? _ref7.length : void 0) > 0) {
            $scope.ann_info.show_quote[thread.message.id] = true;
            _ref8 = thread.message.target;
            for (_n = 0, _len5 = _ref8.length; _n < _len5; _n++) {
              target = _ref8[_n];
              target.highlightQuote = target.quote.replace($scope.quote_regexp, $scope.highlighter);
            }
          } else {
            _ref9 = thread.message.target;
            for (_o = 0, _len6 = _ref9.length; _o < _len6; _o++) {
              target = _ref9[_o];
              target.highlightQuote = target.quote;
            }
            $scope.ann_info.show_quote[thread.message.id] = false;
          }
          children = thread.flattenChildren();
          if (children != null) {
            for (_p = 0, _len7 = children.length; _p < _len7; _p++) {
              child = children[_p];
              child.highlightText = child.text;
              if (_ref10 = child.id, __indexOf.call($scope.search_filter, _ref10) >= 0) {
                $scope.ann_info.shown[child.id] = true;
                _ref11 = $scope.text_regexp;
                for (_q = 0, _len8 = _ref11.length; _q < _len8; _q++) {
                  regexp = _ref11[_q];
                  child.highlightText = child.highlightText.replace(regexp, $scope.highlighter);
                }
              } else {
                $scope.ann_info.shown[child.id] = false;
              }
              $scope.ann_info.more_top[child.id] = setMoreTop(thread.message.id, child);
              $scope.ann_info.more_bottom[child.id] = setMoreBottom(thread.message.id, child);
              $scope.ann_info.show_quote[child.id] = false;
            }
          }
        }
        _ref12 = $scope.render_order;
        for (threadid in _ref12) {
          order = _ref12[threadid];
          hidden = 0;
          last_shown = null;
          for (_r = 0, _len9 = order.length; _r < _len9; _r++) {
            id = order[_r];
            if (__indexOf.call($scope.search_filter, id) >= 0) {
              if (last_shown != null) {
                $scope.ann_info.more_bottom_num[last_shown] = hidden;
              }
              $scope.ann_info.more_top_num[id] = hidden;
              last_shown = id;
              hidden = 0;
            } else {
              hidden += 1;
            }
          }
          if (last_shown != null) {
            $scope.ann_info.more_bottom_num[last_shown] = hidden;
          }
        }
        $rootScope.search_annotations = threads;
        return $scope.threads = threads;
      };
      $scope.$on('$routeUpdate', refresh);
      $scope.getThreadId = function(id) {
        var thread, threadid;
        thread = annotator.threading.getContainer(id);
        threadid = id;
        if (thread.message.references != null) {
          threadid = thread.message.references[0];
        }
        return threadid;
      };
      $scope.clickMoreTop = function(id, $event) {
        var pos, prev_id, rendered, threadid, _results;
        if ($event != null) {
          $event.stopPropagation();
        }
        threadid = $scope.getThreadId(id);
        pos = $scope.render_pos[id];
        rendered = $scope.render_order[threadid];
        $scope.ann_info.more_top[id] = false;
        pos -= 1;
        _results = [];
        while (pos >= 0) {
          prev_id = rendered[pos];
          if ($scope.ann_info.shown[prev_id]) {
            $scope.ann_info.more_bottom[prev_id] = false;
            break;
          }
          $scope.ann_info.more_bottom[prev_id] = false;
          $scope.ann_info.more_top[prev_id] = false;
          $scope.ann_info.shown[prev_id] = true;
          _results.push(pos -= 1);
        }
        return _results;
      };
      $scope.clickMoreBottom = function(id, $event) {
        var next_id, pos, rendered, threadid, _results;
        if ($event != null) {
          $event.stopPropagation();
        }
        threadid = $scope.getThreadId(id);
        pos = $scope.render_pos[id];
        rendered = $scope.render_order[threadid];
        $scope.ann_info.more_bottom[id] = false;
        pos += 1;
        _results = [];
        while (pos < rendered.length) {
          next_id = rendered[pos];
          if ($scope.ann_info.shown[next_id]) {
            $scope.ann_info.more_top[next_id] = false;
            break;
          }
          $scope.ann_info.more_bottom[next_id] = false;
          $scope.ann_info.more_top[next_id] = false;
          $scope.ann_info.shown[next_id] = true;
          _results.push(pos += 1);
        }
        return _results;
      };
      refresh();
    }

    return Search;

  })();

  Notification = (function() {
    Notification.inject = ['$scope'];

    function Notification($scope) {}

    return Notification;

  })();

  angular.module('h.controllers', ['bootstrap', 'h.streamfilter']).controller('AppController', App).controller('AnnotationController', Annotation).controller('EditorController', Editor).controller('ViewerController', Viewer).controller('SearchController', Search).controller('NotificationController', Notification);

}).call(this);
