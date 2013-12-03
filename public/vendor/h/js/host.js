(function() {
  var $,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  $ = Annotator.$;

  Annotator.Host = (function(_super) {
    __extends(Host, _super);

    Host.prototype.drag = {
      delta: 0,
      enabled: false,
      last: null,
      tick: false
    };

    function Host(element, options) {
      this._dragRefresh = __bind(this._dragRefresh, this);
      this._dragUpdate = __bind(this._dragUpdate, this);
      var app, hostOrigin,
        _this = this;
      if (document.baseURI && (window.PDFView != null)) {
        hostOrigin = '*';
      } else {
        hostOrigin = window.location.origin;
        if (hostOrigin == null) {
          hostOrigin = window.location.protocol + "//" + window.location.host;
        }
      }
      app = $('<iframe></iframe>').attr('seamless', '').attr('src', "" + options.app + "#/?xdm=" + (encodeURIComponent(hostOrigin)));
      Host.__super__.constructor.apply(this, arguments);
      app.appendTo(this.frame);
      if (this.plugins.Heatmap != null) {
        this._setupDragEvents();
        this.plugins.Heatmap.element.on('click', function(event) {
          if (_this.frame.hasClass('annotator-collapsed')) {
            return _this.showFrame();
          }
        });
      }
      this.Annotator = Annotator;
      Annotator.$.extend(Annotator.Notification, {
        INFO: 'info',
        ERROR: 'error',
        SUCCESS: 'success'
      });
    }

    Host.prototype._setupXDM = function(options) {
      var channel,
        _this = this;
      channel = Host.__super__._setupXDM.apply(this, arguments);
      return channel.bind('showFrame', function(ctx, routeName) {
        if (!_this.drag.enabled) {
          _this.frame.css({
            'margin-left': "" + (-1 * _this.frame.width()) + "px"
          });
        }
        _this.frame.removeClass('annotator-no-transition');
        _this.frame.removeClass('annotator-collapsed');
        switch (routeName) {
          case 'editor':
            return _this.publish('annotationEditorShown');
          case 'viewer':
            return _this.publish('annotationViewerShown');
        }
      }).bind('hideFrame', function(ctx, routeName) {
        _this.frame.css({
          'margin-left': ''
        });
        _this.frame.removeClass('annotator-no-transition');
        _this.frame.addClass('annotator-collapsed');
        switch (routeName) {
          case 'editor':
            return _this.publish('annotationEditorHidden');
          case 'viewer':
            return _this.publish('annotationViewerHidden');
        }
      }).bind('dragFrame', function(ctx, screenX) {
        return _this._dragUpdate(screenX);
      }).bind('getMaxBottom', function() {
        var all, bottom, el, p, sel, t, x, y, z;
        sel = '*' + ((function() {
          var _i, _len, _ref, _results;
          _ref = ['adder', 'outer', 'notice', 'filter', 'frame'];
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            x = _ref[_i];
            _results.push(":not(.annotator-" + x + ")");
          }
          return _results;
        })()).join('');
        all = (function() {
          var _i, _len, _ref, _ref1, _results;
          _ref = $(document.body).find(sel);
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            el = _ref[_i];
            p = $(el).css('position');
            t = $(el).offset().top;
            z = $(el).css('z-index');
            if ((y = (_ref1 = /\d+/.exec($(el).css('top'))) != null ? _ref1[0] : void 0)) {
              t = Math.min(Number(y, t));
            }
            if ((p === 'absolute' || p === 'fixed') && t === 0 && z !== 'auto') {
              bottom = $(el).outerHeight(false);
              if (bottom > 80) {
                _results.push(0);
              } else {
                _results.push(bottom);
              }
            } else {
              _results.push(0);
            }
          }
          return _results;
        })();
        return Math.max.apply(Math, all);
      }).bind('updateNotificationCounter', function(ctx, count) {
        return _this.publish('updateNotificationCounter', count);
      }).bind('showNotification', function(ctx, n) {
        return _this._pendingNotice = _this.Annotator.showNotification(n.message, n.type);
      }).bind('removeNotification', function() {
        var klass, _, _ref;
        if (_this._pendingNotice == null) {
          return;
        }
        _ref = _this._pendingNotice.options.classes;
        for (_ in _ref) {
          klass = _ref[_];
          _this._pendingNotice.element.removeClass(klass);
        }
        return delete _this._pendingNotice;
      });
    };

    Host.prototype._setupDragEvents = function() {
      var dragEnd, dragStart, el, handle, _i, _len, _ref,
        _this = this;
      el = document.createElementNS('http://www.w3.org/1999/xhtml', 'canvas');
      el.width = el.height = 1;
      this.element.append(el);
      dragStart = function(event) {
        var m;
        event.dataTransfer.dropEffect = 'none';
        event.dataTransfer.effectAllowed = 'none';
        event.dataTransfer.setData('text/plain', '');
        event.dataTransfer.setDragImage(el, 0, 0);
        _this.drag.enabled = true;
        _this.drag.last = event.screenX;
        m = parseInt((getComputedStyle(_this.frame[0])).marginLeft);
        _this.frame.css({
          'margin-left': "" + m + "px"
        });
        return _this.showFrame();
      };
      dragEnd = function(event) {
        _this.drag.enabled = false;
        return _this.drag.last = null;
      };
      _ref = [this.plugins.Heatmap.element[0], this.plugins.Toolbar.buttons[0]];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        handle = _ref[_i];
        handle.draggable = true;
        handle.addEventListener('dragstart', dragStart);
        handle.addEventListener('dragend', dragEnd);
      }
      return document.addEventListener('dragover', function(event) {
        return _this._dragUpdate(event.screenX);
      });
    };

    Host.prototype._dragUpdate = function(screenX) {
      if (!this.drag.enabled) {
        return;
      }
      if (this.drag.last != null) {
        this.drag.delta += screenX - this.drag.last;
      }
      this.drag.last = screenX;
      if (!this.drag.tick) {
        this.drag.tick = true;
        return window.requestAnimationFrame(this._dragRefresh);
      }
    };

    Host.prototype._dragRefresh = function() {
      var d, m, w;
      d = this.drag.delta;
      this.drag.delta = 0;
      this.drag.tick = false;
      m = parseInt((getComputedStyle(this.frame[0])).marginLeft);
      w = -1 * m;
      m += d;
      w -= d;
      this.frame.addClass('annotator-no-transition');
      return this.frame.css({
        'margin-left': "" + m + "px",
        width: "" + w + "px"
      });
    };

    return Host;

  })(Annotator.Guest);

}).call(this);
