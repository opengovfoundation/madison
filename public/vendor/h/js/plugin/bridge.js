(function() {
  var $,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    __slice = [].slice;

  $ = Annotator.$;

  Annotator.Plugin.Bridge = (function(_super) {
    __extends(Bridge, _super);

    Bridge.prototype.events = {
      'beforeAnnotationCreated': 'beforeAnnotationCreated',
      'annotationCreated': 'annotationCreated',
      'annotationUpdated': 'annotationUpdated',
      'annotationDeleted': 'annotationDeleted',
      'annotationsLoaded': 'annotationsLoaded',
      'enableAnnotating': 'enableAnnotating'
    };

    Bridge.prototype.options = {
      origin: '*',
      scope: 'annotator:bridge',
      gateway: false,
      onConnect: function() {
        return true;
      },
      formatter: function(annotation) {
        return annotation;
      },
      parser: function(annotation) {
        return annotation;
      },
      merge: function(local, remote) {
        var k, v;
        for (k in remote) {
          v = remote[k];
          local[k] = v;
        }
        return local;
      }
    };

    Bridge.prototype.cache = null;

    Bridge.prototype.links = null;

    Bridge.prototype.updating = null;

    function Bridge(elem, options) {
      this.annotationsLoaded = __bind(this.annotationsLoaded, this);
      this.annotationDeleted = __bind(this.annotationDeleted, this);
      this.annotationUpdated = __bind(this.annotationUpdated, this);
      this.annotationCreated = __bind(this.annotationCreated, this);
      this.beforeAnnotationCreated = __bind(this.beforeAnnotationCreated, this);
      this._onMessage = __bind(this._onMessage, this);
      var window;
      if (options.window != null) {
        window = options.window;
        delete options.window;
        Bridge.__super__.constructor.call(this, elem, options);
        this.options.window = window;
      } else {
        Bridge.__super__.constructor.apply(this, arguments);
      }
      this.cache = {};
      this.links = [];
      this.updating = {};
    }

    Bridge.prototype.pluginInit = function() {
      $(window).on('message', this._onMessage);
      return this._beacon();
    };

    Bridge.prototype._tag = function(msg, tag) {
      if (msg.$$tag) {
        return msg;
      }
      tag = tag || (window.btoa(Math.random()));
      Object.defineProperty(msg, '$$tag', {
        value: tag
      });
      this.cache[tag] = msg;
      return msg;
    };

    Bridge.prototype._parse = function(_arg) {
      var local, merged, msg, remote, tag;
      tag = _arg.tag, msg = _arg.msg;
      local = this.cache[tag];
      remote = this.options.parser(msg);
      if (local != null) {
        merged = this.options.merge(local, remote);
      } else {
        merged = remote;
      }
      return this._tag(merged, tag);
    };

    Bridge.prototype._format = function(annotation) {
      var msg;
      this._tag(annotation);
      msg = this.options.formatter(annotation);
      return {
        tag: annotation.$$tag,
        msg: msg
      };
    };

    Bridge.prototype._build = function(options) {
      var channel,
        _this = this;
      if ((options.origin.match(/^chrome-extension:\/\//)) || (options.origin.match(/^resource:\/\//))) {
        options.origin = '*';
      }
      console.log("Bridge plugin connecting to " + options.origin);
      return channel = Channel.build(options).bind('setupAnnotation', function(txn, annotation) {
        return _this._format(_this.annotator.setupAnnotation(_this._parse(annotation)));
      }).bind('beforeCreateAnnotation', function(txn, annotation) {
        annotation = _this._parse(annotation);
        delete _this.cache[annotation.$$tag];
        _this.annotator.publish('beforeAnnotationCreated', annotation);
        _this.cache[annotation.$$tag] = annotation;
        return _this._format(annotation);
      }).bind('createAnnotation', function(txn, annotation) {
        annotation = _this._parse(annotation);
        delete _this.cache[annotation.$$tag];
        _this.annotator.publish('annotationCreated', annotation);
        _this.cache[annotation.$$tag] = annotation;
        return _this._format(annotation);
      }).bind('updateAnnotation', function(txn, annotation) {
        annotation = _this._parse(annotation);
        delete _this.cache[annotation.$$tag];
        annotation = _this.annotator.updateAnnotation(annotation);
        _this.cache[annotation.$$tag] = annotation;
        return _this._format(annotation);
      }).bind('deleteAnnotation', function(txn, annotation) {
        var res;
        annotation = _this._parse(annotation);
        delete _this.cache[annotation.$$tag];
        annotation = _this.annotator.deleteAnnotation(annotation);
        res = _this._format(annotation);
        delete _this.cache[annotation.$$tag];
        return res;
      }).bind('loadAnnotations', function(txn, annotations) {
        var a, _i, _len;
        for (_i = 0, _len = annotations.length; _i < _len; _i++) {
          a = annotations[_i];
          if (_this.cache[a.tag]) {
            _this._parse(a);
          }
        }
        annotations = (function() {
          var _j, _len1, _results;
          _results = [];
          for (_j = 0, _len1 = annotations.length; _j < _len1; _j++) {
            a = annotations[_j];
            if (!this.cache[a.tag]) {
              _results.push(this._parse(a));
            }
          }
          return _results;
        }).call(_this);
        if (annotations.length) {
          return _this.annotator.loadAnnotations(annotations);
        }
      }).bind('showEditor', function(ctx, annotation) {
        return _this.annotator.showEditor(_this._parse(annotation));
      }).bind('enableAnnotating', function(ctx, state) {
        return _this.annotator.enableAnnotating(state, false);
      });
    };

    Bridge.prototype._beacon = function() {
      var child, parent, queue, _results;
      queue = [window.top];
      _results = [];
      while (queue.length) {
        parent = queue.shift();
        if (parent !== window) {
          console.log(window.location.toString(), 'sending beacon...');
          parent.postMessage('__annotator_dhcp_discovery', this.options.origin);
        }
        _results.push((function() {
          var _i, _len, _ref, _results1;
          _ref = parent.frames;
          _results1 = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            child = _ref[_i];
            _results1.push(queue.push(child));
          }
          return _results1;
        })());
      }
      return _results;
    };

    Bridge.prototype._call = function(options) {
      var deferreds, _makeDestroyFn,
        _this = this;
      _makeDestroyFn = function(c) {
        return function(error, reason) {
          var l;
          c.destroy();
          return _this.links = (function() {
            var _i, _len, _ref, _results;
            _ref = this.links;
            _results = [];
            for (_i = 0, _len = _ref.length; _i < _len; _i++) {
              l = _ref[_i];
              if (l.channel !== c) {
                _results.push(l);
              }
            }
            return _results;
          }).call(_this);
        };
      };
      deferreds = this.links.map(function(l) {
        var d;
        d = $.Deferred().fail(_makeDestroyFn(l.channel));
        options = $.extend({}, options, {
          success: function(result) {
            return d.resolve(result);
          },
          error: function(error, reason) {
            if (error !== 'timeout_error') {
              console.log('Error in call! Reason: ' + reason);
              console.log(error);
              console.log('Destroying channel!');
              return d.reject(error, reason);
            } else {
              return d.resolve(null);
            }
          },
          timeout: 1000
        });
        l.channel.call(options);
        return d.promise();
      });
      return $.when.apply($, deferreds).then(function() {
        var annotation, r, results, _i, _len;
        results = 1 <= arguments.length ? __slice.call(arguments, 0) : [];
        annotation = {};
        for (_i = 0, _len = results.length; _i < _len; _i++) {
          r = results[_i];
          if (r !== null) {
            $.extend(annotation, _this._parse(r));
          }
        }
        return typeof options.callback === "function" ? options.callback(null, annotation) : void 0;
      }).fail(function(failure) {
        return typeof options.callback === "function" ? options.callback(failure) : void 0;
      });
    };

    Bridge.prototype._notify = function(options) {
      var l, _i, _len, _ref, _results;
      _ref = this.links;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        l = _ref[_i];
        _results.push(l.channel.notify(options));
      }
      return _results;
    };

    Bridge.prototype._onMessage = function(e) {
      var channel, data, match, options, origin, scope, source, _ref,
        _this = this;
      _ref = e.originalEvent, source = _ref.source, origin = _ref.origin, data = _ref.data;
      match = typeof data.match === "function" ? data.match(/^__annotator_dhcp_(discovery|ack|offer)(:\d+)?$/) : void 0;
      if (!match) {
        return;
      }
      if (match[1] === 'discovery') {
        if (this.options.gateway) {
          scope = ':' + ('' + Math.random()).replace(/\D/g, '');
          source.postMessage('__annotator_dhcp_offer' + scope, origin);
        } else {
          source.postMessage('__annotator_dhcp_ack', origin);
          return;
        }
      } else if (match[1] === 'ack') {
        if (this.options.gateway) {
          scope = ':' + ('' + Math.random()).replace(/\D/g, '');
          source.postMessage('__annotator_dhcp_offer' + scope, origin);
        } else {
          return;
        }
      } else if (match[1] === 'offer') {
        if (this.options.gateway) {
          return;
        } else {
          scope = match[2];
        }
      }
      scope = this.options.scope + scope;
      options = $.extend({}, this.options, {
        window: source,
        origin: origin,
        scope: scope,
        onReady: function() {
          var a, annotations, t;
          options.onConnect.call(_this.annotator, source, origin, scope);
          annotations = (function() {
            var _ref1, _results;
            _ref1 = this.cache;
            _results = [];
            for (t in _ref1) {
              a = _ref1[t];
              _results.push(this._format(a));
            }
            return _results;
          }).call(_this);
          if (annotations.length) {
            return channel.notify({
              method: 'loadAnnotations',
              params: annotations
            });
          }
        }
      });
      channel = this._build(options);
      return this.links.push({
        channel: channel,
        window: source
      });
    };

    Bridge.prototype.beforeAnnotationCreated = function(annotation) {
      if (annotation.$$tag != null) {
        return;
      }
      this.beforeCreateAnnotation(annotation);
      return this;
    };

    Bridge.prototype.annotationCreated = function(annotation) {
      if (!((annotation.$$tag != null) && this.cache[annotation.$$tag])) {
        return;
      }
      this.createAnnotation(annotation);
      return this;
    };

    Bridge.prototype.annotationUpdated = function(annotation) {
      if (!((annotation.$$tag != null) && this.cache[annotation.$$tag])) {
        return;
      }
      this.updateAnnotation(annotation);
      return this;
    };

    Bridge.prototype.annotationDeleted = function(annotation) {
      var _this = this;
      if (!((annotation.$$tag != null) && this.cache[annotation.$$tag])) {
        return;
      }
      this.deleteAnnotation(annotation, function(err) {
        if (err) {
          return _this.annotator.setupAnnotation(annotation);
        } else {
          return delete _this.cache[annotation.$$tag];
        }
      });
      return this;
    };

    Bridge.prototype.annotationsLoaded = function(annotations) {
      var a;
      this._notify({
        method: 'loadAnnotations',
        params: (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = annotations.length; _i < _len; _i++) {
            a = annotations[_i];
            _results.push(this._format(a));
          }
          return _results;
        }).call(this)
      });
      return this;
    };

    Bridge.prototype.beforeCreateAnnotation = function(annotation, cb) {
      this._call({
        method: 'beforeCreateAnnotation',
        params: this._format(annotation),
        callback: cb
      });
      return annotation;
    };

    Bridge.prototype.setupAnnotation = function(annotation, cb) {
      this._call({
        method: 'setupAnnotation',
        params: this._format(annotation),
        callback: cb
      });
      return annotation;
    };

    Bridge.prototype.createAnnotation = function(annotation, cb) {
      this._call({
        method: 'createAnnotation',
        params: this._format(annotation),
        callback: cb
      });
      return annotation;
    };

    Bridge.prototype.updateAnnotation = function(annotation, cb) {
      this._call({
        method: 'updateAnnotation',
        params: this._format(annotation),
        callback: cb
      });
      return annotation;
    };

    Bridge.prototype.deleteAnnotation = function(annotation, cb) {
      this._call({
        method: 'deleteAnnotation',
        params: this._format(annotation),
        callback: cb
      });
      return annotation;
    };

    Bridge.prototype.showEditor = function(annotation) {
      this._notify({
        method: 'showEditor',
        params: this._format(annotation)
      });
      return this;
    };

    Bridge.prototype.enableAnnotating = function(state) {
      return this._notify({
        method: 'enableAnnotating',
        params: state
      });
    };

    return Bridge;

  })(Annotator.Plugin);

}).call(this);
