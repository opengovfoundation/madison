(function() {
  var $,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  $ = Annotator.$;

  Annotator.Guest = (function(_super) {
    __extends(Guest, _super);

    Guest.prototype.events = {
      ".annotator-adder button click": "onAdderClick",
      ".annotator-adder button mousedown": "onAdderMousedown",
      "setTool": "onSetTool",
      "setVisibleHighlights": "onSetVisibleHighlights"
    };

    Guest.prototype.options = {
      TextHighlights: {},
      DomTextMapper: {},
      TextAnchors: {},
      FuzzyTextAnchors: {},
      PDF: {},
      Document: {}
    };

    Guest.prototype.comments = null;

    Guest.prototype.tool = 'comment';

    Guest.prototype.visibleHighlights = false;

    Guest.prototype.noBack = false;

    function Guest(element, options, config) {
      var name, opts, _ref,
        _this = this;
      if (config == null) {
        config = {};
      }
      this.onSetVisibleHighlights = __bind(this.onSetVisibleHighlights, this);
      this.onAdderClick = __bind(this.onAdderClick, this);
      this.addToken = __bind(this.addToken, this);
      this.onAnchorClick = __bind(this.onAnchorClick, this);
      this.onAnchorMousedown = __bind(this.onAnchorMousedown, this);
      this.checkForStartSelection = __bind(this.checkForStartSelection, this);
      this.removeEmphasis = __bind(this.removeEmphasis, this);
      this.addEmphasis = __bind(this.addEmphasis, this);
      this.showEditor = __bind(this.showEditor, this);
      this.updateViewer = __bind(this.updateViewer, this);
      this.showViewer = __bind(this.showViewer, this);
      this.scanDocument = __bind(this.scanDocument, this);
      Gettext.prototype.parse_locale_data(annotator_locale_data);
      options.noScan = true;
      Guest.__super__.constructor.apply(this, arguments);
      delete this.options.noScan;
      this.comments = [];
      this.frame = $('<div></div>').appendTo(this.wrapper).addClass('annotator-frame annotator-outer annotator-collapsed');
      delete this.options.app;
      this.addPlugin('Bridge', {
        formatter: function(annotation) {
          var formatted, k, v, _ref;
          formatted = {};
          if (annotation.document != null) {
            formatted['uri'] = _this.plugins.Document.uri();
          }
          for (k in annotation) {
            v = annotation[k];
            if (k !== 'anchors') {
              formatted[k] = v;
            }
          }
          if ((_ref = formatted.document) != null ? _ref.title : void 0) {
            formatted.document.title = formatted.document.title.slice();
          }
          return formatted;
        },
        onConnect: function(source, origin, scope) {
          _this.publish("enableAnnotating", _this.canAnnotate);
          return _this.panel = _this._setupXDM({
            window: source,
            origin: origin,
            scope: "" + scope + ":provider",
            onReady: function() {
              console.log("Guest functions are ready for " + origin);
              return setTimeout(function() {
                var event;
                event = document.createEvent("UIEvents");
                event.initUIEvent("annotatorReady", false, false, window, 0);
                event.annotator = _this;
                return window.dispatchEvent(event);
              });
            }
          });
        }
      });
      _ref = this.options;
      for (name in _ref) {
        if (!__hasProp.call(_ref, name)) continue;
        opts = _ref[name];
        if (!this.plugins[name]) {
          this.addPlugin(name, opts);
        }
      }
      if (!config.dontScan) {
        this.scanDocument("Guest initialized");
      }
      this.subscribe('annotationDeleted', function(annotation) {
        var i, _ref1;
        if (_this.isComment(annotation)) {
          i = _this.comments.indexOf(annotation);
          if (i !== -1) {
            [].splice.apply(_this.comments, [i, i - i + 1].concat(_ref1 = [])), _ref1;
            return _this.plugins.Heatmap._update();
          }
        }
      });
    }

    Guest.prototype._setupXDM = function(options) {
      var channel,
        _this = this;
      if ((options.origin.match(/^chrome-extension:\/\//)) || (options.origin.match(/^resource:\/\//))) {
        options.origin = '*';
      }
      channel = Channel.build(options);
      return channel.bind('onEditorHide', this.onEditorHide).bind('onEditorSubmit', this.onEditorSubmit).bind('setDynamicBucketMode', function(ctx, value) {
        if (!_this.plugins.Heatmap) {
          return;
        }
        _this.plugins.Heatmap.dynamicBucket = value;
        if (value) {
          return _this.plugins.Heatmap._update();
        }
      }).bind('setActiveHighlights', function(ctx, tags) {
        var hl, _i, _len, _ref, _ref1;
        if (tags == null) {
          tags = [];
        }
        _ref = _this.getHighlights();
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          hl = _ref[_i];
          if (_ref1 = hl.annotation.$$tag, __indexOf.call(tags, _ref1) >= 0) {
            hl.setActive(true, true);
          } else {
            if (!hl.isTemporary()) {
              hl.setActive(false, true);
            }
          }
        }
        return _this.publish("finalizeHighlights");
      }).bind('scrollTo', function(ctx, tag) {
        var hl, _i, _len, _ref;
        _ref = _this.getHighlights();
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
          hl = _ref[_i];
          if (hl.annotation.$$tag === tag) {
            hl.scrollTo();
            return;
          }
        }
      }).bind('adderClick', function() {
        _this.selectedTargets = _this.forcedLoginTargets;
        _this.onAdderClick(_this.forcedLoginEvent);
        delete _this.forcedLoginTargets;
        return delete _this.forcedLoginEvent;
      }).bind('getDocumentInfo', function() {
        return {
          uri: _this.plugins.Document.uri(),
          metadata: _this.plugins.Document.metadata
        };
      }).bind('setTool', function(ctx, name) {
        _this.setTool(name);
        return _this.publish('setTool', name);
      }).bind('setVisibleHighlights', function(ctx, state) {
        _this.setVisibleHighlights(state, false);
        return _this.publish('setVisibleHighlights', state);
      });
    };

    Guest.prototype.scanDocument = function(reason) {
      var e;
      if (reason == null) {
        reason = "something happened";
      }
      try {
        console.log("Analyzing host frame, because " + reason + "...");
        return this._scan();
      } catch (_error) {
        e = _error;
        console.log(e.message);
        return console.log(e.stack);
      }
    };

    Guest.prototype._setupWrapper = function() {
      var _this = this;
      this.wrapper = this.element.on('click', function() {
        if (_this.canAnnotate && !_this.noBack && !_this.creatingHL) {
          setTimeout(function() {
            var _ref;
            if (!((_ref = _this.selectedTargets) != null ? _ref.length : void 0)) {
              return _this.hideFrame();
            }
          });
        }
        return delete _this.creatingHL;
      });
      return this;
    };

    Guest.prototype._setupViewer = function() {
      return this;
    };

    Guest.prototype._setupEditor = function() {
      return this;
    };

    Guest.prototype.showViewer = function(annotations) {
      var a, _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: "showViewer",
        params: (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = annotations.length; _i < _len; _i++) {
            a = annotations[_i];
            _results.push(a.id);
          }
          return _results;
        })()
      }) : void 0;
    };

    Guest.prototype.updateViewer = function(annotations) {
      var a, _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: "updateViewer",
        params: (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = annotations.length; _i < _len; _i++) {
            a = annotations[_i];
            _results.push(a.id);
          }
          return _results;
        })()
      }) : void 0;
    };

    Guest.prototype.showEditor = function(annotation) {
      return this.plugins.Bridge.showEditor(annotation);
    };

    Guest.prototype.addEmphasis = function(annotations) {
      var a, _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: "addEmphasis",
        params: (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = annotations.length; _i < _len; _i++) {
            a = annotations[_i];
            _results.push(a.id);
          }
          return _results;
        })()
      }) : void 0;
    };

    Guest.prototype.removeEmphasis = function(annotations) {
      var a, _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: "removeEmphasis",
        params: (function() {
          var _i, _len, _results;
          _results = [];
          for (_i = 0, _len = annotations.length; _i < _len; _i++) {
            a = annotations[_i];
            _results.push(a.id);
          }
          return _results;
        })()
      }) : void 0;
    };

    Guest.prototype.checkForStartSelection = function(event) {
      if (!(event && this.isAnnotator(event.target))) {
        return this.mouseIsDown = true;
      }
    };

    Guest.prototype.confirmSelection = function() {
      var quote;
      if (this.selectedTargets.length !== 1) {
        return true;
      }
      quote = this.plugins.TextAnchors.getQuoteForTarget(this.selectedTargets[0]);
      if (quote.length > 2) {
        return true;
      }
      return confirm("You have selected a very short piece of text: only " + length + " chars. Are you sure you want to highlight this?");
    };

    Guest.prototype.onSuccessfulSelection = function(event, immediate) {
      var annotation;
      this.selectedTargets = event.targets;
      if (this.tool === 'highlight') {
        if (!this.canAnnotate) {
          return false;
        }
        if (!this.confirmSelection()) {
          return false;
        }
        this.creatingHL = true;
        annotation = {
          inject: true
        };
        annotation = this.setupAnnotation(annotation);
        this.publish('beforeAnnotationCreated', annotation);
        return this.publish('annotationCreated', annotation);
      } else {
        return Guest.__super__.onSuccessfulSelection.apply(this, arguments);
      }
    };

    Guest.prototype.onAnchorMouseover = function(annotations) {
      if ((this.tool === 'highlight') || this.visibleHighlights) {
        return this.addEmphasis(annotations);
      }
    };

    Guest.prototype.onAnchorMouseout = function(annotations) {
      if ((this.tool === 'highlight') || this.visibleHighlights) {
        return this.removeEmphasis(annotations);
      }
    };

    Guest.prototype.onAnchorMousedown = function(annotations) {
      if ((this.tool === 'highlight') || this.visibleHighlights) {
        return this.noBack = true;
      }
    };

    Guest.prototype.onAnchorClick = function(annotations) {
      if (!((this.tool === 'highlight') || this.visibleHighlights && this.noBack)) {
        return;
      }
      this.showViewer(annotations);
      return this.noBack = false;
    };

    Guest.prototype.setTool = function(name) {
      var _ref;
      this.tool = name;
      return (_ref = this.panel) != null ? _ref.notify({
        method: 'setTool',
        params: name
      }) : void 0;
    };

    Guest.prototype.setVisibleHighlights = function(state, notify) {
      var markerClass, _ref;
      if (state == null) {
        state = true;
      }
      if (notify == null) {
        notify = true;
      }
      if (notify) {
        return (_ref = this.panel) != null ? _ref.notify({
          method: 'setVisibleHighlights',
          params: state
        }) : void 0;
      } else {
        markerClass = 'annotator-highlights-always-on';
        if (state || this.tool === 'highlight') {
          return this.element.addClass(markerClass);
        } else {
          return this.element.removeClass(markerClass);
        }
      }
    };

    Guest.prototype.addComment = function() {
      var sel;
      sel = this.selectedTargets;
      this.selectedTargets = [];
      this.onAdderClick();
      return this.selectedTargets = sel;
    };

    Guest.prototype.isComment = function(annotation) {
      var _ref, _ref1;
      return !(annotation.inject || ((_ref = annotation.references) != null ? _ref.length : void 0) || ((_ref1 = annotation.target) != null ? _ref1.length : void 0));
    };

    Guest.prototype.setupAnnotation = function(annotation) {
      annotation = Guest.__super__.setupAnnotation.apply(this, arguments);
      if (this.isComment(annotation)) {
        this.comments.push(annotation);
      }
      return annotation;
    };

    Guest.prototype.showFrame = function() {
      var _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: 'open'
      }) : void 0;
    };

    Guest.prototype.hideFrame = function() {
      var _ref;
      return (_ref = this.panel) != null ? _ref.notify({
        method: 'back'
      }) : void 0;
    };

    Guest.prototype.addToken = function(token) {
      return this.api.notify({
        method: 'addToken',
        params: token
      });
    };

    Guest.prototype.onAdderClick = function(event) {
      "Differs from upstream in a few ways:\n- Don't fire annotationCreated events: that's the job of the sidebar\n- Save the event for retriggering if login interrupts the flow";
      var annotation, cancel, cleanup, hl, position, save, _i, _len, _ref,
        _this = this;
      if (event != null) {
        event.preventDefault();
      }
      this.forcedLoginEvent = event;
      this.forcedLoginTargets = this.selectedTargets;
      this.adder.hide();
      this.inAdderClick = false;
      position = this.adder.position();
      annotation = this.setupAnnotation(this.createAnnotation());
      _ref = this.getHighlights([annotation]);
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        hl = _ref[_i];
        hl.setTemporary(true);
      }
      save = function() {
        var _j, _len1, _ref1, _results;
        cleanup();
        _ref1 = _this.getHighlights([annotation]);
        _results = [];
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          hl = _ref1[_j];
          _results.push(hl.setTemporary(false));
        }
        return _results;
      };
      cancel = function() {
        cleanup();
        return _this.deleteAnnotation(annotation);
      };
      cleanup = function() {
        _this.unsubscribe('annotationEditorHidden', cancel);
        return _this.unsubscribe('annotationEditorSubmit', save);
      };
      this.subscribe('annotationEditorHidden', cancel);
      this.subscribe('annotationEditorSubmit', save);
      return this.showEditor(annotation, position);
    };

    Guest.prototype.onSetTool = function(name) {
      switch (name) {
        case 'comment':
          return this.setVisibleHighlights(this.visibleHighlights, false);
        case 'highlight':
          return this.setVisibleHighlights(true, false);
      }
    };

    Guest.prototype.onSetVisibleHighlights = function(state) {
      this.visibleHighlights = state;
      return this.setVisibleHighlights(state, false);
    };

    Guest.prototype.deleteAnnotation = function(annotation) {
      if (annotation.deleted) {
        return;
      } else {
        annotation.deleted = true;
      }
      return Guest.__super__.deleteAnnotation.apply(this, arguments);
    };

    return Guest;

  })(Annotator);

}).call(this);
