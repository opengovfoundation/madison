(function() {
  var FlashProvider, flashInterceptor;

  FlashProvider = (function() {
    FlashProvider.prototype.queues = {
      '': [],
      info: [],
      error: [],
      success: []
    };

    FlashProvider.prototype.notice = null;

    FlashProvider.prototype.timeout = null;

    function FlashProvider() {
      angular.extend(Annotator.Notification, {
        INFO: 'info',
        ERROR: 'error',
        SUCCESS: 'success'
      });
    }

    FlashProvider.prototype._process = function() {
      var msg, msgs, notice, q, _ref, _ref1, _results,
        _this = this;
      this.timeout = null;
      _ref = this.queues;
      _results = [];
      for (q in _ref) {
        msgs = _ref[q];
        if (msgs.length) {
          msg = msgs.shift();
          if (!q) {
            _ref1 = msg, q = _ref1[0], msg = _ref1[1];
          }
          if (annotator.isOpen()) {
            notice = Annotator.showNotification(msg, q);
            this.timeout = this._wait(function() {
              var klass, _, _ref2;
              _ref2 = notice.options.classes;
              for (_ in _ref2) {
                klass = _ref2[_];
                notice.element.removeClass(klass);
              }
              return _this._process();
            });
          } else {
            annotator.host.notify({
              method: "showNotification",
              params: {
                message: msg,
                type: q
              }
            });
            this.timeout = this._wait(function() {
              annotator.host.notify({
                method: "removeNotification"
              });
              return _this._process();
            });
          }
          break;
        } else {
          _results.push(void 0);
        }
      }
      return _results;
    };

    FlashProvider.prototype._flash = function(queue, messages) {
      var _ref;
      if (this.queues[queue] != null) {
        this.queues[queue] = (_ref = this.queues[queue]) != null ? _ref.concat(messages) : void 0;
        if (this.timeout == null) {
          return this._process();
        }
      }
    };

    FlashProvider.prototype.$get = [
      '$timeout', function($timeout) {
        this._wait = function(cb) {
          return $timeout(cb, 5000);
        };
        return angular.bind(this, this._flash);
      }
    ];

    return FlashProvider;

  })();

  flashInterceptor = [
    '$q', 'flash', function($q, flash) {
      return {
        response: function(response) {
          var data, format, ignoreStatus, msgs, q, _ref;
          data = response.data;
          format = response.headers('content-type');
          if (format != null ? format.match(/^application\/json/) : void 0) {
            if (data.flash != null) {
              _ref = data.flash;
              for (q in _ref) {
                msgs = _ref[q];
                if (msgs.length === 2 && msgs[0] === "Invalid username or password." && msgs[1] === msgs[0]) {
                  msgs.pop();
                  ignoreStatus = true;
                }
                flash(q, msgs);
              }
            }
            if (data.status === 'failure') {
              if (!ignoreStatus) {
                flash('error', data.reason);
              }
              return $q.reject(data.reason);
            } else if (data.status === 'okay') {
              response.data = data.model;
              return response;
            }
          } else {
            return response;
          }
        }
      };
    }
  ];

  angular.module('h.flash', ['ngResource']).provider('flash', FlashProvider).factory('flashInterceptor', flashInterceptor).config([
    '$httpProvider', function($httpProvider) {
      return $httpProvider.interceptors.push('flashInterceptor');
    }
  ]);

}).call(this);
