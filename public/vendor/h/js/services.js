(function() {
  var AuthenticationProvider, DraftProvider, Hypothesis,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; },
    __slice = [].slice;

  Hypothesis = (function(_super) {
    __extends(Hypothesis, _super);

    Hypothesis.prototype.events = {
      'annotationCreated': 'updateAncestors',
      'annotationUpdated': 'updateAncestors',
      'annotationDeleted': 'updateAncestors',
      'serviceDiscovery': 'serviceDiscovery'
    };

    Hypothesis.prototype.options = {
      noMatching: true,
      Discovery: {},
      Permissions: {
        permissions: {
          read: ['group:__world__']
        },
        userAuthorize: function(action, annotation, user) {
          var token, tokens, _i, _len;
          if (annotation.permissions) {
            tokens = annotation.permissions[action] || [];
            if (tokens.length === 0) {
              return false;
            }
            for (_i = 0, _len = tokens.length; _i < _len; _i++) {
              token = tokens[_i];
              if (this.userId(user) === token) {
                return true;
              }
              if (token === 'group:__world__') {
                return true;
              }
              if (token === 'group:__authenticated__' && (this.user != null)) {
                return true;
              }
            }
            return false;
          } else if (annotation.user) {
            return user && this.userId(user) === this.userId(annotation.user);
          }
          return true;
        },
        showEditPermissionsCheckbox: false,
        showViewPermissionsCheckbox: false,
        userString: function(user) {
          return user.replace(/^acct:(.+)@(.+)$/, '$1 on $2');
        }
      },
      Threading: {}
    };

    Hypothesis.prototype.ongoing_edit = null;

    Hypothesis.prototype.providers = null;

    Hypothesis.prototype.host = null;

    Hypothesis.prototype.tool = 'comment';

    Hypothesis.prototype.visibleHighlights = false;

    Hypothesis.prototype.viewer = {
      addField: (function() {})
    };

    Hypothesis.$inject = ['$document', '$location', '$rootScope', '$route', '$window', 'authentication', 'drafts'];

    function Hypothesis($document, $location, $rootScope, $route, $window, authentication, drafts) {
      this.setVisibleHighlights = __bind(this.setVisibleHighlights, this);
      this.setTool = __bind(this.setTool, this);
      this.serviceDiscovery = __bind(this.serviceDiscovery, this);
      this.updateAncestors = __bind(this.updateAncestors, this);
      this.isOpen = __bind(this.isOpen, this);
      this.hide = __bind(this.hide, this);
      this.show = __bind(this.show, this);
      this.showEditor = __bind(this.showEditor, this);
      this.clickAdder = __bind(this.clickAdder, this);
      this.showViewer = __bind(this.showViewer, this);
      this.updateViewer = __bind(this.updateViewer, this);
      this.buildReplyList = __bind(this.buildReplyList, this);
      var buffer, event, name, opts, whitelist, _i, _len, _ref, _ref1,
        _this = this;
      Gettext.prototype.parse_locale_data(annotator_locale_data);
      Hypothesis.__super__.constructor.call(this, $document.find('body'));
      window.annotator = this;
      buffer = new Array(16);
      uuid.v4(null, buffer, 0);
      this.clientID = uuid.unparse(buffer);
      $.ajaxSetup({
        headers: {
          "x-client-id": this.clientID
        }
      });
      this.auth = authentication;
      this.providers = [];
      this.socialView = {
        name: "none"
      };
      this.patch_store();
      _ref = this.options;
      for (name in _ref) {
        if (!__hasProp.call(_ref, name)) continue;
        opts = _ref[name];
        if (!this.plugins[name] && name in Annotator.Plugin) {
          this.addPlugin(name, opts);
        }
      }
      whitelist = ['diffHTML', 'inject', 'quote', 'ranges', 'target', 'id', 'references', 'uri', 'diffCaseOnly'];
      this.addPlugin('Bridge', {
        gateway: true,
        formatter: function(annotation) {
          var formatted, k, v, _ref1;
          formatted = {};
          for (k in annotation) {
            v = annotation[k];
            if (__indexOf.call(whitelist, k) >= 0) {
              formatted[k] = v;
            }
          }
          if ((annotation.thread != null) && ((_ref1 = annotation.thread) != null ? _ref1.children.length : void 0)) {
            formatted.reply_count = annotation.thread.flattenChildren().length;
          } else {
            formatted.reply_count = 0;
          }
          return formatted;
        },
        parser: function(annotation) {
          var k, parsed, v;
          parsed = {};
          for (k in annotation) {
            v = annotation[k];
            if (__indexOf.call(whitelist, k) >= 0) {
              parsed[k] = v;
            }
          }
          return parsed;
        },
        onConnect: function(source, origin, scope) {
          var channel, entities, options;
          options = {
            window: source,
            origin: origin,
            scope: "" + scope + ":provider",
            onReady: function() {
              console.log("Provider functions are ready for " + origin);
              if (source === _this.element.injector().get('$window').parent) {
                return _this.host = channel;
              }
            }
          };
          entities = [];
          channel = _this._setupXDM(options);
          channel.call({
            method: 'getDocumentInfo',
            success: function(info) {
              var entityUris, href, link, _i, _len, _ref1, _ref2;
              entityUris = {};
              entityUris[info.uri] = true;
              _ref1 = info.metadata.link;
              for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
                link = _ref1[_i];
                if (link.href) {
                  entityUris[link.href] = true;
                }
              }
              for (href in entityUris) {
                entities.push(href);
              }
              return (_ref2 = _this.plugins.Store) != null ? _ref2.loadAnnotations() : void 0;
            }
          });
          channel.notify({
            method: 'setTool',
            params: _this.tool
          });
          channel.notify({
            method: 'setVisibleHighlights',
            params: _this.visibleHighlights
          });
          return _this.providers.push({
            channel: channel,
            entities: entities
          });
        }
      });
      this.subscribe('beforeAnnotationCreated', function(annotation) {
        var permissions, userId;
        if (annotation.target == null) {
          annotation.target = [];
        }
        if (annotation.highlights == null) {
          annotation.highlights = [];
        }
        if (annotation.inject) {
          permissions = _this.plugins.Permissions;
          userId = permissions.options.userId(permissions.user);
          return annotation.permissions = {
            read: [userId],
            admin: [userId],
            update: [userId],
            "delete": [userId]
          };
        }
      });
      _ref1 = ['beforeAnnotationCreated', 'beforeAnnotationUpdated'];
      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
        event = _ref1[_i];
        this.subscribe(event, function(annotation) {
          var action, permissions, roles, userId, _ref2, _results;
          permissions = _this.plugins.Permissions;
          if (permissions.user != null) {
            userId = permissions.options.userId(permissions.user);
            _ref2 = annotation.permissions;
            _results = [];
            for (action in _ref2) {
              roles = _ref2[action];
              if (__indexOf.call(roles, userId) < 0) {
                _results.push(roles.push(userId));
              } else {
                _results.push(void 0);
              }
            }
            return _results;
          }
        });
      }
      $rootScope.annotations = [];
      $rootScope.search_annotations = [];
      this.subscribe('annotationCreated', function(a) {
        if (a.references == null) {
          return $rootScope.annotations.unshift(a);
        }
      });
      this.subscribe('annotationDeleted', function(a) {
        $rootScope.annotations = $rootScope.annotations.filter(function(b) {
          return b !== a;
        });
        return $rootScope.search_annotations = $rootScope.search_annotations.filter(function(b) {
          return b.message != null;
        });
      });
    }

    Hypothesis.prototype._setupXDM = function(options) {
      var $rootScope, provider,
        _this = this;
      $rootScope = this.element.injector().get('$rootScope');
      if ((options.origin.match(/^chrome-extension:\/\//)) || (options.origin.match(/^resource:\/\//))) {
        options.origin = '*';
      }
      provider = Channel.build(options);
      return provider.bind('publish', function() {
        var args, ctx;
        ctx = arguments[0], args = 2 <= arguments.length ? __slice.call(arguments, 1) : [];
        return _this.publish.apply(_this, args);
      }).bind('back', function() {
        return $rootScope.$apply(function() {
          if (!_this.discardDrafts()) {
            return;
          }
          return _this.hide();
        });
      }).bind('open', function() {
        return $rootScope.$apply(function() {
          return _this.show();
        });
      }).bind('showViewer', function(ctx, ids) {
        var id;
        if (ids == null) {
          ids = [];
        }
        return _this.showViewer((function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = ids.length; _i < _len; _i++) {
            id = ids[_i];
            _results.push((this.threading.getContainer(id)).message);
          }
          return _results;
        }).call(_this));
      }).bind('updateViewer', function(ctx, ids) {
        var id;
        if (ids == null) {
          ids = [];
        }
        return _this.updateViewer((function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = ids.length; _i < _len; _i++) {
            id = ids[_i];
            _results.push((this.threading.getContainer(id)).message);
          }
          return _results;
        }).call(_this));
      }).bind('setTool', function(ctx, name) {
        return $rootScope.$apply(function() {
          return _this.setTool(name);
        });
      }).bind('setVisibleHighlights', function(ctx, state) {
        return $rootScope.$apply(function() {
          return _this.setVisibleHighlights(state);
        });
      });
    };

    Hypothesis.prototype._setupWrapper = function() {
      this.wrapper = this.element.find('#wrapper').on('mousewheel', function(event, delta) {
        var $current, $parent, scrollEnd, scrollTop, _ref;
        $current = $(event.target);
        while ((_ref = $current.css('overflow')) === 'hidden' || _ref === 'visible') {
          $parent = $current.parent();
          if ($parent.get(0).nodeType === 9) {
            event.preventDefault();
            return;
          }
          $current = $parent;
        }
        scrollTop = $current[0].scrollTop;
        scrollEnd = $current[0].scrollHeight - $current[0].clientHeight;
        if (delta > 0 && scrollTop === 0) {
          return event.preventDefault();
        } else if (delta < 0 && scrollEnd - scrollTop <= 5) {
          return event.preventDefault();
        }
      });
      return this;
    };

    Hypothesis.prototype._setupDocumentEvents = function() {
      var _this = this;
      return document.addEventListener('dragover', function(event) {
        var p, _i, _len, _ref, _results;
        _ref = _this.providers;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          p = _ref[_i];
          _results.push(p.channel.notify({
            method: 'dragFrame',
            params: event.screenX
          }));
        }
        return _results;
      });
    };

    Hypothesis.prototype._setupDynamicStyle = function() {
      return this;
    };

    Hypothesis.prototype._setupViewer = function() {
      return this;
    };

    Hypothesis.prototype._setupEditor = function() {
      return this;
    };

    Hypothesis.prototype.getHtmlQuote = function(quote) {
      return quote;
    };

    Hypothesis.prototype.setupAnnotation = function(annotation) {
      annotation.highlights = [];
      return annotation;
    };

    Hypothesis.prototype.sortAnnotations = function(a, b) {
      var a_upd, b_upd;
      a_upd = a.updated != null ? new Date(a.updated) : new Date();
      b_upd = b.updated != null ? new Date(b.updated) : new Date();
      return a_upd.getTime() - b_upd.getTime();
    };

    Hypothesis.prototype.buildReplyList = function(annotations) {
      var $filter, annotation, children, r, thread, _i, _len, _results;
      if (annotations == null) {
        annotations = [];
      }
      $filter = this.element.injector().get('$filter');
      _results = [];
      for (_i = 0, _len = annotations.length; _i < _len; _i++) {
        annotation = annotations[_i];
        if (annotation != null) {
          thread = this.threading.getContainer(annotation.id);
          children = (function() {
            var _j, _len1, _ref, _results1;
            _ref = thread.children || [];
            _results1 = [];
            for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
              r = _ref[_j];
              _results1.push(r.message);
            }
            return _results1;
          })();
          annotation.reply_list = children.sort(this.sortAnnotations).reverse();
          _results.push(this.buildReplyList(children));
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    Hypothesis.prototype.updateViewer = function(annotations) {
      var _this = this;
      if (annotations == null) {
        annotations = [];
      }
      annotations = annotations.filter(function(a) {
        return a != null;
      });
      this.element.injector().invoke([
        '$location', '$rootScope', function($location, $rootScope) {
          _this.buildReplyList(annotations);
          $rootScope.annotations = annotations;
          return $rootScope.$digest();
        }
      ]);
      return this;
    };

    Hypothesis.prototype.showViewer = function(annotations) {
      var _this = this;
      if (annotations == null) {
        annotations = [];
      }
      annotations = annotations.filter(function(a) {
        return a != null;
      });
      this.show();
      this.element.injector().invoke([
        '$location', '$rootScope', function($location, $rootScope) {
          _this.buildReplyList(annotations);
          $rootScope.annotations = annotations;
          $location.path('/viewer').replace();
          return $rootScope.$digest();
        }
      ]);
      return this;
    };

    Hypothesis.prototype.clickAdder = function() {
      var p, _i, _len, _ref, _results;
      _ref = this.providers;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        p = _ref[_i];
        _results.push(p.channel.notify({
          method: 'adderClick'
        }));
      }
      return _results;
    };

    Hypothesis.prototype.showEditor = function(annotation) {
      var _this = this;
      this.show();
      this.element.injector().invoke([
        '$location', '$rootScope', '$route', 'drafts', function($location, $rootScope, $route, drafts) {
          var p, search, _i, _len, _ref;
          _this.ongoing_edit = annotation;
          if (!((_this.plugins.Auth != null) && _this.plugins.Auth.haveValidToken())) {
            $route.current.locals.$scope.$apply(function() {
              return $route.current.locals.$scope.$emit('showAuth', true);
            });
            _ref = _this.providers;
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              p = _ref[_i];
              p.channel.notify({
                method: 'onEditorHide'
              });
            }
            return;
          }
          search = {
            id: annotation.id,
            action: 'create'
          };
          $location.path('/editor').search(search);
          drafts.add(annotation);
          return $rootScope.$digest();
        }
      ]);
      return this;
    };

    Hypothesis.prototype.show = function() {
      return this.element.scope().frame.visible = true;
    };

    Hypothesis.prototype.hide = function() {
      return this.element.scope().frame.visible = false;
    };

    Hypothesis.prototype.isOpen = function() {
      return this.element.scope().frame.visible;
    };

    Hypothesis.prototype.patch_store = function() {
      var $location, $rootScope, Store,
        _this = this;
      $location = this.element.injector().get('$location');
      $rootScope = this.element.injector().get('$rootScope');
      Store = Annotator.Plugin.Store;
      Store.prototype.loadAnnotations = function() {
        var p, query, uri, _i, _len, _ref, _results;
        query = {
          limit: 1000
        };
        this.annotator.considerSocialView.call(this.annotator, query);
        if (this.entities == null) {
          this.entities = {};
        }
        _ref = this.annotator.providers;
        _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          p = _ref[_i];
          _results.push((function() {
            var _j, _len1, _ref1, _results1;
            _ref1 = p.entities;
            _results1 = [];
            for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
              uri = _ref1[_j];
              if (this.entities[uri] == null) {
                console.log("Loading annotations for: " + uri);
                this.entities[uri] = true;
                _results1.push(this.loadAnnotationsFromSearch(angular.extend(query, {
                  uri: uri
                })));
              } else {
                _results1.push(void 0);
              }
            }
            return _results1;
          }).call(this));
        }
        return _results;
      };
      return Store.prototype.updateAnnotation = function(annotation, data) {
        var search, thread, _ref;
        if (!Object.keys(data).length) {
          return;
        }
        if ((annotation.id != null) && annotation.id !== data.id) {
          thread = _this.threading.getContainer(annotation.id);
          thread.id = data.id;
          _this.threading.idTable[data.id] = thread;
          delete _this.threading.idTable[annotation.id];
          Object.defineProperty(annotation, 'id', {
            configurable: true,
            enumerable: true,
            writable: true
          });
          search = $location.search();
          if ((search != null) && search.id === annotation.id) {
            search.id = data.id;
            $location.search(search).replace();
          }
        }
        annotation = angular.extend(annotation, data);
        if ((_ref = _this.plugins.Bridge) != null) {
          _ref.updateAnnotation(annotation);
        }
        return $rootScope.$digest();
      };
    };

    Hypothesis.prototype.considerSocialView = function(query) {
      var p;
      switch (this.socialView.name) {
        case "none":
          console.log("Not applying any Social View filters.");
          return delete query.user;
        case "single-player":
          if ((p = this.auth.persona) != null) {
            console.log("Social View filter: single player mode.");
            return query.user = "acct:" + p.username + "@" + p.provider;
          } else {
            console.log("Social View: single-player mode, but ignoring it, since not logged in.");
            return delete query.user;
          }
          break;
        default:
          return console.warn("Unsupported Social View: '" + this.socialView.name + "'!");
      }
    };

    Hypothesis.prototype.updateAncestors = function(annotation) {
      var $timeout, ref, rel, _i, _len, _ref, _ref1, _results,
        _this = this;
      _ref1 = ((_ref = annotation.references) != null ? _ref.slice().reverse() : void 0) || [];
      _results = [];
      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
        ref = _ref1[_i];
        rel = (this.threading.getContainer(ref)).message;
        if (rel != null) {
          $timeout = this.element.injector().get('$timeout');
          $timeout((function() {
            return _this.plugins.Bridge.updateAnnotation(rel);
          }), 10);
          this.updateAncestors(rel);
          break;
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    Hypothesis.prototype.serviceDiscovery = function(options) {
      var _base;
      if ((_base = this.options).Store == null) {
        _base.Store = {};
      }
      angular.extend(this.options.Store, options);
      return this.addPlugin('Store', this.options.Store);
    };

    Hypothesis.prototype.setTool = function(name) {
      var p, scope, _i, _len, _ref, _results;
      if (name === this.tool) {
        return;
      }
      if (!this.discardDrafts()) {
        return;
      }
      if (name === 'highlight') {
        if (!((this.plugins.Auth != null) && this.plugins.Auth.haveValidToken())) {
          scope = this.element.scope();
          scope.ongoingHighlightSwitch = true;
          scope.skipAuthChangeReload = true;
          scope.sheet.collapsed = false;
          scope.sheet.tab = 'login';
          this.show();
          return;
        }
        this.socialView.name = 'single-player';
      } else {
        this.socialView.name = 'none';
      }
      this.tool = name;
      this.publish('setTool', name);
      _ref = this.providers;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        p = _ref[_i];
        _results.push(p.channel.notify({
          method: 'setTool',
          params: name
        }));
      }
      return _results;
    };

    Hypothesis.prototype.setVisibleHighlights = function(state) {
      var p, _i, _len, _ref, _results;
      if (state === this.visibleHighlights) {
        return;
      }
      this.visibleHighlights = state;
      this.publish('setVisibleHighlights', state);
      _ref = this.providers;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        p = _ref[_i];
        _results.push(p.channel.notify({
          method: 'setVisibleHighlights',
          params: state
        }));
      }
      return _results;
    };

    Hypothesis.prototype.isComment = function(annotation) {
      var _ref, _ref1;
      return !(((_ref = annotation.references) != null ? _ref.length : void 0) || ((_ref1 = annotation.target) != null ? _ref1.length : void 0));
    };

    Hypothesis.prototype.discardDrafts = function() {
      return this.element.injector().get('drafts').discard();
    };

    return Hypothesis;

  })(Annotator);

  AuthenticationProvider = (function() {
    function AuthenticationProvider() {
      var action, _i, _len, _ref;
      this.actions = {
        load: {
          method: 'GET',
          withCredentials: true
        }
      };
      _ref = ['login', 'logout', 'register', 'forgot', 'activate'];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        action = _ref[_i];
        this.actions[action] = {
          method: 'POST',
          params: {
            '__formid__': action
          },
          withCredentials: true
        };
      }
      this.actions['claim'] = this.actions['forgot'];
    }

    AuthenticationProvider.prototype.$get = [
      '$document', '$resource', function($document, $resource) {
        var baseUrl;
        baseUrl = $document[0].baseURI.replace(/:(\d+)/, '\\:$1');
        baseUrl = baseUrl.replace(/#$/, '');
        baseUrl = baseUrl.replace(/\/*$/, '/');
        return $resource(baseUrl, {}, this.actions).load();
      }
    ];

    return AuthenticationProvider;

  })();

  DraftProvider = (function() {
    DraftProvider.prototype.drafts = null;

    function DraftProvider() {
      this.drafts = [];
    }

    DraftProvider.prototype.$get = function() {
      return this;
    };

    DraftProvider.prototype.add = function(draft, cb) {
      return this.drafts.push({
        draft: draft,
        cb: cb
      });
    };

    DraftProvider.prototype.remove = function(draft) {
      var d, i, remove, _i, _len, _ref, _results;
      remove = [];
      _ref = this.drafts;
      for (i = _i = 0, _len = _ref.length; _i < _len; i = ++_i) {
        d = _ref[i];
        if (d.draft === draft) {
          remove.push(i);
        }
      }
      _results = [];
      while (remove.length) {
        _results.push(this.drafts.splice(remove.pop(), 1));
      }
      return _results;
    };

    DraftProvider.prototype.contains = function(draft) {
      var d, _i, _len, _ref;
      _ref = this.drafts;
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        d = _ref[_i];
        if (d.draft === draft) {
          return true;
        }
      }
      return false;
    };

    DraftProvider.prototype.discard = function() {
      var d, discarded, text, _i, _len;
      text = (function() {
        switch (this.drafts.length) {
          case 0:
            return null;
          case 1:
            return "You have an unsaved reply.\n\nDo you really want to discard this draft?";
          default:
            return "You have " + this.drafts.length + " unsaved replies.\n\nDo you really want to discard these drafts?";
        }
      }).call(this);
      if (this.drafts.length === 0 || confirm(text)) {
        discarded = this.drafts.slice();
        this.drafts = [];
        for (_i = 0, _len = discarded.length; _i < _len; _i++) {
          d = discarded[_i];
          if (typeof d.cb === "function") {
            d.cb();
          }
        }
        return true;
      } else {
        return false;
      }
    };

    return DraftProvider;

  })();

  angular.module('h.services', ['ngResource', 'h.filters']).provider('authentication', AuthenticationProvider).provider('drafts', DraftProvider).service('annotator', Hypothesis);

}).call(this);
