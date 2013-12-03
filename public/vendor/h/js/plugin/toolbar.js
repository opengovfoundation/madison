(function() {
  var $, _ref,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  $ = Annotator.$;

  Annotator.Plugin.Toolbar = (function(_super) {
    __extends(Toolbar, _super);

    function Toolbar() {
      _ref = Toolbar.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    Toolbar.prototype.events = {
      '.annotator-toolbar li:first-child mouseenter': 'show',
      '.annotator-toolbar mouseleave': 'hide',
      'updateNotificationCounter': 'onUpdateNotificationCounter',
      'setTool': 'onSetTool',
      'setVisibleHighlights': 'onSetVisibleHighlights'
    };

    Toolbar.prototype.html = {
      element: '<div class="annotator-toolbar annotator-hide"></div>',
      notification: '<div class="annotator-notification-counter"></div>'
    };

    Toolbar.prototype.options = {
      items: [
        {
          "title": "Toggle Sidebar",
          "class": "tri-icon",
          "click": function(event) {
            var collapsed;
            event.preventDefault();
            event.stopPropagation();
            collapsed = window.annotator.frame.hasClass('annotator-collapsed');
            if (collapsed) {
              return window.annotator.showFrame();
            } else {
              return window.annotator.hideFrame();
            }
          }
        }, {
          "title": "Show Annotations",
          "class": "alwaysonhighlights-icon",
          "click": function(event) {
            var state;
            event.preventDefault();
            event.stopPropagation();
            state = !window.annotator.visibleHighlights;
            return window.annotator.setVisibleHighlights(state);
          }
        }, {
          "title": "Highlighting Mode",
          "class": "highlighter-icon",
          "click": function(event) {
            var state, tool;
            event.preventDefault();
            event.stopPropagation();
            state = !(window.annotator.tool === 'highlight');
            tool = state ? 'highlight' : 'comment';
            return window.annotator.setTool(tool);
          }
        }, {
          "title": "New Comment",
          "class": "commenter-icon",
          "click": function(event) {
            event.preventDefault();
            event.stopPropagation();
            return window.annotator.addComment();
          }
        }
      ]
    };

    Toolbar.prototype.pluginInit = function() {
      var list,
        _this = this;
      this.annotator.toolbar = this.toolbar = $(this.html.element);
      if (this.options.container != null) {
        $(this.options.container).append(this.toolbar);
      } else {
        $(this.element).append(this.toolbar);
      }
      this.notificationCounter = $(this.html.notification);
      this.toolbar.append(this.notificationCounter);
      this.buttons = this.options.items.reduce(function(buttons, item) {
        var anchor, button;
        anchor = $('<a></a>').attr('href', '').attr('title', item.title).on('click', item.click).addClass(item["class"]);
        button = $('<li></li>').append(anchor);
        return buttons.add(button);
      }, $());
      list = $('<ul></ul>');
      this.buttons.appendTo(list);
      return this.toolbar.append(list);
    };

    Toolbar.prototype.show = function() {
      return this.toolbar.removeClass('annotator-hide');
    };

    Toolbar.prototype.hide = function() {
      return this.toolbar.addClass('annotator-hide');
    };

    Toolbar.prototype.onUpdateNotificationCounter = function(count) {
      var element;
      element = $(this.buttons[0]);
      element.toggle('fg_highlight', {
        color: 'lightblue'
      });
      setTimeout(function() {
        return element.toggle('fg_highlight', {
          color: 'lightblue'
        });
      }, 500);
      switch (false) {
        case !(count > 9):
          return this.notificationCounter.text('>9');
        case !((0 < count && count <= 9)):
          return this.notificationCounter.text("+" + count);
        default:
          return this.notificationCounter.text('');
      }
    };

    Toolbar.prototype.onSetTool = function(name) {
      if (name === 'highlight') {
        $(this.buttons[2]).addClass('pushed');
      } else {
        $(this.buttons[2]).removeClass('pushed');
      }
      return this._updateStickyButtons();
    };

    Toolbar.prototype.onSetVisibleHighlights = function(state) {
      if (state) {
        $(this.buttons[1]).addClass('pushed');
      } else {
        $(this.buttons[1]).removeClass('pushed');
      }
      return this._updateStickyButtons();
    };

    Toolbar.prototype._updateStickyButtons = function() {
      var count, height, _ref1, _ref2;
      count = $(this.buttons).filter(function() {
        return $(this).hasClass('pushed');
      }).length;
      if (count) {
        height = (count + 1) * 32;
        this.toolbar.css("min-height", "" + height + "px");
      } else {
        height = 32;
        this.toolbar.css("min-height", "");
      }
      if ((_ref1 = this.annotator.plugins.Heatmap) != null) {
        _ref1.BUCKET_THRESHOLD_PAD = height - 9;
      }
      return (_ref2 = this.annotator.plugins.Heatmap) != null ? _ref2._update() : void 0;
    };

    return Toolbar;

  })(Annotator.Plugin);

}).call(this);
