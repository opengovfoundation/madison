(function() {
  var $,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; },
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  $ = Annotator.$;

  Annotator.Plugin.Heatmap = (function(_super) {
    __extends(Heatmap, _super);

    Heatmap.prototype.BUCKET_THRESHOLD_PAD = 25;

    Heatmap.prototype.BUCKET_SIZE = 50;

    Heatmap.prototype.BOTTOM_CORRECTION = 14;

    Heatmap.prototype.html = "<div class=\"annotator-heatmap\">\n  <svg xmlns=\"http://www.w3.org/2000/svg\"\n       version=\"1.1\">\n     <defs>\n       <linearGradient id=\"heatmap-gradient\" x2=\"0\" y2=\"100%\">\n       </linearGradient>\n       <filter id=\"heatmap-blur\">\n         <feGaussianBlur stdDeviation=\"0 2\"></feGaussianBlur>\n       </filter>\n     </defs>\n     <rect x=\"0\" y=\"0\" width=\"100%\" height=\"100%\"\n           fill=\"url('#heatmap-gradient')\"\n           filter=\"url('#heatmap-blur')\" >\n     </rect>\n   </svg>\n</div>";

    Heatmap.prototype.options = {
      gapSize: 60
    };

    Heatmap.prototype.buckets = [];

    Heatmap.prototype.index = [];

    Heatmap.prototype.dynamicBucket = true;

    function Heatmap(element, options) {
      this.isComment = __bind(this.isComment, this);
      this.isLower = __bind(this.isLower, this);
      this.isUpper = __bind(this.isUpper, this);
      this._fillDynamicBucket = __bind(this._fillDynamicBucket, this);
      this._update = __bind(this._update, this);
      this._scheduleUpdate = __bind(this._scheduleUpdate, this);
      Heatmap.__super__.constructor.call(this, $(this.html), options);
      if (this.options.container != null) {
        $(this.options.container).append(this.element);
      } else {
        $(element).append(this.element);
      }
    }

    Heatmap.prototype.pluginInit = function() {
      var event, events, _i, _len,
        _this = this;
      if (typeof d3 === "undefined" || d3 === null) {
        return;
      }
      this._maybeRebaseUrls();
      events = ['annotationCreated', 'annotationUpdated', 'annotationDeleted', 'annotationsLoaded'];
      for (_i = 0, _len = events.length; _i < _len; _i++) {
        event = events[_i];
        if (event === 'annotationCreated') {
          this.annotator.subscribe(event, function() {
            _this.dynamicBucket = false;
            return _this._scheduleUpdate();
          });
        } else {
          this.annotator.subscribe(event, this._scheduleUpdate);
        }
      }
      this.element.on('click', function(event) {
        event.stopPropagation();
        _this._fillDynamicBucket();
        return _this.dynamicBucket = true;
      });
      $(window).on('resize scroll', this._update);
      $(document.body).on('resize scroll', '*', this._update);
      this.annotator.subscribe("highlightsCreated", function(highlights) {
        var anchor, dir, next, page;
        anchor = Array.isArray(highlights) ? highlights[0].anchor : highlights.anchor;
        if (anchor.annotation.id != null) {
          _this._scheduleUpdate();
        }
        if ((_this.pendingScroll != null) && __indexOf.call(_this.pendingScroll.anchors, anchor) >= 0) {
          if (!--_this.pendingScroll.count) {
            page = _this.pendingScroll.page;
            dir = _this.pendingScroll.direction === "up" ? +1 : -1;
            next = _this.pendingScroll.anchors.reduce(function(acc, anchor) {
              var hl, start;
              start = acc.start, next = acc.next;
              hl = anchor.highlight[page];
              if ((next == null) || hl.getTop() * dir > start * dir) {
                return {
                  start: hl.getTop(),
                  next: hl
                };
              } else {
                return acc;
              }
            }, {}).next;
            next.paddedScrollDownTo();
            return delete _this.pendingScroll;
          }
        }
      });
      this.annotator.subscribe("highlightRemoved", function(highlight) {
        if (highlight.annotation.id != null) {
          return _this._scheduleUpdate();
        }
      });
      return addEventListener("docPageScrolling", function() {
        return _this._update();
      });
    };

    Heatmap.prototype._scheduleUpdate = function() {
      var _this = this;
      if (this._updatePending) {
        return;
      }
      this._updatePending = true;
      return setTimeout((function() {
        delete _this._updatePending;
        return _this._update();
      }), 200);
    };

    Heatmap.prototype._maybeRebaseUrls = function() {
      var base, fill, filter, location, rect;
      if (!document.getElementsByTagName('base').length) {
        return;
      }
      location = window.location;
      base = "" + location.protocol + "//" + location.host + location.pathname;
      rect = this.element.find('rect');
      fill = rect.attr('fill');
      filter = rect.attr('filter');
      fill = fill.replace(/(#\w+)/, "" + base + "$1");
      filter = filter.replace(/(#\w+)/, "" + base + "$1");
      rect.attr('fill', fill);
      return rect.attr('filter', filter);
    };

    Heatmap.prototype._collate = function(a, b) {
      var i, _i, _ref;
      for (i = _i = 0, _ref = a.length - 1; 0 <= _ref ? _i <= _ref : _i >= _ref; i = 0 <= _ref ? ++_i : --_i) {
        if (a[i] < b[i]) {
          return -1;
        }
        if (a[i] > b[i]) {
          return 1;
        }
      }
      return 0;
    };

    Heatmap.prototype._colorize = function(v) {
      var c;
      c = d3.scale.pow().exponent(2).domain([0, 1]).range(['#f7fbff', '#08306b']).interpolate(d3.interpolateHcl);
      return c(v).toString();
    };

    Heatmap.prototype._collectVirtualAnnotations = function(startPage, endPage) {
      var anchor, anchors, page, results, _i;
      results = [];
      for (page = _i = startPage; startPage <= endPage ? _i <= endPage : _i >= endPage; page = startPage <= endPage ? ++_i : --_i) {
        anchors = this.annotator.anchors[page];
        if (anchors != null) {
          $.merge(results, (function() {
            var _j, _len, _results;
            _results = [];
            for (_j = 0, _len = anchors.length; _j < _len; _j++) {
              anchor = anchors[_j];
              if (!anchor.fullyRealized) {
                _results.push(anchor.annotation);
              }
            }
            return _results;
          })());
        }
      }
      return results;
    };

    Heatmap.prototype._jumpMinMax = function(annotations, direction) {
      var anchor, dir, hl, next, startPage;
      if (direction !== "up" && direction !== "down") {
        throw "Direction is mandatory!";
      }
      dir = direction === "up" ? +1 : -1;
      next = annotations.reduce(function(acc, ann) {
        var anchor, hl, start, _ref;
        start = acc.start, next = acc.next;
        anchor = ann.anchors[0];
        if ((next == null) || start.page * dir < anchor.startPage * dir) {
          return {
            start: {
              page: anchor.startPage,
              top: (_ref = anchor.highlight[anchor.startPage]) != null ? _ref.getTop() : void 0
            },
            next: [anchor]
          };
        } else if (start.page === anchor.startPage) {
          hl = anchor.highlight[start.page];
          if (hl != null) {
            if (start.top * dir < hl.getTop() * dir) {
              return {
                start: {
                  page: start.page,
                  top: hl.getTop()
                },
                next: [anchor]
              };
            } else {
              return acc;
            }
          } else {
            return {
              start: {
                page: start.page
              },
              next: $.merge(next, [anchor])
            };
          }
        } else {
          return acc;
        }
      }, {}).next;
      anchor = next[0];
      startPage = anchor.startPage;
      if (this.annotator.domMapper.isPageMapped(startPage)) {
        hl = anchor.highlight[startPage];
        return hl.paddedScrollTo(direction);
      } else {
        this.pendingScroll = {
          anchors: next,
          count: next.length,
          page: startPage,
          direction: direction
        };
        return this.annotator.domMapper.setPageIndex(startPage);
      }
    };

    Heatmap.prototype._update = function() {
      var above, b, below, comments, currentPage, defaultView, element, firstPage, highlights, info, lastPage, mapper, max, opacity, points, stopData, stops, tabs, temp, wrapper, _i, _len, _ref, _ref1,
        _this = this;
      wrapper = this.annotator.wrapper;
      highlights = this.annotator.getHighlights();
      defaultView = wrapper[0].ownerDocument.defaultView;
      above = [];
      below = [];
      mapper = this.annotator.domMapper;
      firstPage = 0;
      currentPage = mapper.getPageIndex();
      lastPage = mapper.getPageCount() - 1;
      $.merge(above, this._collectVirtualAnnotations(0, currentPage - 1));
      $.merge(below, this._collectVirtualAnnotations(currentPage + 1, lastPage));
      comments = this.annotator.comments.slice();
      points = highlights.reduce(function(points, hl, i) {
        var d, h, x;
        d = hl.annotation;
        x = hl.getTop() - wrapper.offset().top - defaultView.pageYOffset;
        h = hl.getHeight();
        if (x <= _this.BUCKET_SIZE + _this.BUCKET_THRESHOLD_PAD) {
          if (__indexOf.call(above, d) < 0) {
            above.push(d);
          }
        } else if (x + h >= $(window).height() - _this.BUCKET_SIZE) {
          if (__indexOf.call(below, d) < 0) {
            below.push(d);
          }
        } else {
          points.push([x, 1, d]);
          points.push([x + h, -1, d]);
        }
        return points;
      }, []);
      _ref = points.sort(this._collate).reduce(function(_arg, _arg1, i, points) {
        var a, a0, buckets, carry, d, index, j, last, toMerge, x, _i, _j, _len, _len1, _ref, _ref1;
        buckets = _arg.buckets, index = _arg.index, carry = _arg.carry;
        x = _arg1[0], d = _arg1[1], a = _arg1[2];
        if (d > 0) {
          if ((j = carry.annotations.indexOf(a)) < 0) {
            carry.annotations.unshift(a);
            carry.counts.unshift(1);
          } else {
            carry.counts[j]++;
          }
        } else {
          j = carry.annotations.indexOf(a);
          if (--carry.counts[j] === 0) {
            carry.annotations.splice(j, 1);
            carry.counts.splice(j, 1);
          }
        }
        if ((index.length === 0 || i === points.length - 1) || carry.annotations.length === 0 || x - index[index.length - 1] > _this.options.gapSize) {
          buckets.push(carry.annotations.slice());
          index.push(x);
        } else {
          if ((_ref = buckets[buckets.length - 2]) != null ? _ref.length : void 0) {
            last = buckets[buckets.length - 2];
            toMerge = buckets.pop();
            index.pop();
          } else {
            last = buckets[buckets.length - 1];
            toMerge = [];
          }
          _ref1 = carry.annotations;
          for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
            a0 = _ref1[_i];
            if (__indexOf.call(last, a0) < 0) {
              last.push(a0);
            }
          }
          for (_j = 0, _len1 = toMerge.length; _j < _len1; _j++) {
            a0 = toMerge[_j];
            if (__indexOf.call(last, a0) < 0) {
              last.push(a0);
            }
          }
        }
        return {
          buckets: buckets,
          index: index,
          carry: carry
        };
      }, {
        buckets: [],
        index: [],
        carry: {
          annotations: [],
          counts: [],
          latest: 0
        }
      }), this.buckets = _ref.buckets, this.index = _ref.index;
      this.buckets.unshift([], above, []);
      this.buckets.push(below);
      this.buckets.push(comments, []);
      this.index.unshift(0, this.BUCKET_THRESHOLD_PAD, this.BUCKET_THRESHOLD_PAD + this.BUCKET_SIZE);
      this.index.push($(window).height() - this.BUCKET_SIZE);
      if (comments.length) {
        this.index.push($(window).height() - this.BUCKET_SIZE + this.BOTTOM_CORRECTION * 2);
        this.index.push($(window).height() + this.BUCKET_SIZE - this.BOTTOM_CORRECTION * 3);
      } else {
        this.index.push($(window).height() + this.BOTTOM_CORRECTION);
        this.index.push($(window).height() + this.BOTTOM_CORRECTION);
      }
      max = 0;
      _ref1 = this.buckets;
      for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
        b = _ref1[_i];
        info = b.reduce(function(info, a) {
          var subtotal;
          subtotal = a.reply_count || 0;
          return {
            top: info.top + 1,
            replies: (info.replies || 0) + subtotal,
            total: (info.total || 0) + subtotal + 1
          };
        }, {
          top: 0,
          replies: 0,
          total: 0
        });
        max = Math.max(max, info.total);
        b.total = info.total;
        b.top = info.top;
        b.replies = info.replies;
        temp = b.top + '+' + b.replies;
        b.display = temp.length < 5 && b.replies > 0 ? temp : b.total;
      }
      stopData = $.map(this.buckets, function(bucket, i) {
        var curve, end, offsets, start, v, x2, _j, _len1, _ref2, _ref3, _ref4, _results;
        x2 = _this.index[i + 1] != null ? _this.index[i + 1] : wrapper.height();
        offsets = [_this.index[i], x2];
        if (bucket.total) {
          start = ((_ref2 = _this.buckets[i - 1]) != null ? _ref2.total : void 0) && ((_this.buckets[i - 1].total + bucket.total) / 2) || 1e-6;
          end = ((_ref3 = _this.buckets[i + 1]) != null ? _ref3.total : void 0) && ((_this.buckets[i + 1].total + bucket.total) / 2) || 1e-6;
          curve = d3.scale.pow().exponent(.1).domain([0, .5, 1]).range([[offsets[0], i, 0, start], [d3.mean(offsets), i, .5, bucket.total], [offsets[1], i, 1, end]]).interpolate(d3.interpolateArray);
          _ref4 = d3.range(0, 1, .05);
          _results = [];
          for (_j = 0, _len1 = _ref4.length; _j < _len1; _j++) {
            v = _ref4[_j];
            _results.push(curve(v));
          }
          return _results;
        } else {
          return [[offsets[0], i, 0, 1e-6], [offsets[1], i, 1, 1e-6]];
        }
      });
      element = d3.select(this.element[0]);
      opacity = d3.scale.pow().domain([0, max]).range([.1, .6]).exponent(2);
      stops = element.select('#heatmap-gradient').selectAll('stop').data(stopData, function(d) {
        return d;
      });
      stops.enter().append('stop');
      stops.exit().remove();
      stops.order().attr('offset', function(v) {
        return v[0] / $(window).height();
      }).attr('stop-color', function(v) {
        if (max === 0) {
          return _this._colorize(1e-6);
        } else {
          return _this._colorize(v[3] / max);
        }
      }).attr('stop-opacity', function(v) {
        if (max === 0) {
          return .1;
        } else {
          return opacity(v[3]);
        }
      });
      tabs = element.selectAll('div.heatmap-pointer').data(function() {
        var buckets;
        buckets = [];
        _this.index.forEach(function(b, i) {
          if (_this.buckets[i].length > 0 || _this.isUpper(i) || _this.isLower(i)) {
            return buckets.push(i);
          }
        });
        return buckets;
      });
      tabs.enter().append('div').classed('heatmap-pointer', true).on('mousemove', function(bucket) {
        var hl, _j, _len1, _ref2, _ref3;
        _ref2 = _this.annotator.getHighlights();
        for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
          hl = _ref2[_j];
          if (_ref3 = hl.annotation, __indexOf.call(_this.buckets[bucket], _ref3) >= 0) {
            hl.setActive(true, true);
          } else {
            if (!hl.isTemporary()) {
              hl.setActive(false, true);
            }
          }
        }
        return _this.annotator.publish("finalizeHighlights");
      }).on('mouseout', function() {
        var hl, _j, _len1, _ref2;
        _ref2 = _this.annotator.getHighlights();
        for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
          hl = _ref2[_j];
          if (!hl.isTemporary()) {
            hl.setActive(false, true);
          }
        }
        return _this.annotator.publish("finalizeHighlights");
      }).on('click', function(bucket) {
        var pad;
        d3.event.stopPropagation();
        pad = defaultView.innerHeight * .2;
        if (_this.isUpper(bucket)) {
          _this.dynamicBucket = true;
          return _this._jumpMinMax(_this.buckets[bucket], "up");
        } else if (_this.isLower(bucket)) {
          _this.dynamicBucket = true;
          return _this._jumpMinMax(_this.buckets[bucket], "down");
        } else {
          d3.event.stopPropagation();
          _this.dynamicBucket = false;
          return annotator.showViewer(_this.buckets[bucket]);
        }
      });
      tabs.exit().remove();
      tabs.style('top', function(d) {
        return "" + ((_this.index[d] + _this.index[d + 1]) / 2) + "px";
      }).html(function(d) {
        return "<div class='label'>" + _this.buckets[d].display + "</div><div class='svg'></div>";
      }).classed('upper', this.isUpper).classed('lower', this.isLower).classed('commenter', this.isComment).style('display', function(d) {
        if (_this.buckets[d].length === 0) {
          return 'none';
        } else {
          return '';
        }
      });
      if (this.dynamicBucket) {
        return this._fillDynamicBucket();
      }
    };

    Heatmap.prototype._fillDynamicBucket = function() {
      var anchors, bottom, top, visible,
        _this = this;
      top = window.pageYOffset;
      bottom = top + $(window).innerHeight();
      anchors = this.annotator.getHighlights();
      visible = anchors.reduce(function(acc, hl) {
        var _ref, _ref1;
        if ((top <= (_ref = hl.getTop()) && _ref <= bottom)) {
          if (_ref1 = hl.annotation, __indexOf.call(acc, _ref1) < 0) {
            acc.push(hl.annotation);
          }
        }
        return acc;
      }, []);
      return this.annotator.updateViewer(visible);
    };

    Heatmap.prototype.isUpper = function(i) {
      return i === 1;
    };

    Heatmap.prototype.isLower = function(i) {
      return i === this.index.length - 3;
    };

    Heatmap.prototype.isComment = function(i) {
      return i === this.index.length - 2;
    };

    return Heatmap;

  })(Annotator.Plugin);

}).call(this);
