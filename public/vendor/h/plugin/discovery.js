(function() {
  var _ref,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Annotator.Plugin.Discovery = (function(_super) {
    __extends(Discovery, _super);

    function Discovery() {
      _ref = Discovery.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    Discovery.prototype.pluginInit = function() {
      var href, svc,
        _this = this;
      console.log("Initializing discovery plugin.");
      svc = $('link').filter(function() {
        return this.rel === 'service' && this.type === 'application/annotatorsvc+json';
      }).filter(function() {
        return this.href;
      });
      if (!svc.length) {
        return;
      }
      href = svc[0].href;
      return $.getJSON(href, function(data) {
        var action, info, options, url, _ref1, _ref2, _ref3;
        if ((data != null ? data.links : void 0) == null) {
          return;
        }
        options = {
          prefix: href.replace(/\/$/, ''),
          urls: {}
        };
        if (((_ref1 = data.links.search) != null ? _ref1.url : void 0) != null) {
          options.urls.search = data.links.search.url;
        }
        _ref2 = data.links.annotation || {};
        for (action in _ref2) {
          info = _ref2[action];
          if (info.url != null) {
            options.urls[action] = info.url;
          }
        }
        _ref3 = options.urls;
        for (action in _ref3) {
          url = _ref3[action];
          options.urls[action] = url.replace(options.prefix, '');
        }
        return _this.annotator.publish('serviceDiscovery', options);
      });
    };

    return Discovery;

  })(Annotator.Plugin);

}).call(this);
