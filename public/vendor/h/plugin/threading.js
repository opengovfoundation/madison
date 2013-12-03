(function() {
  var _ref,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; },
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Annotator.Plugin.Threading = (function(_super) {
    __extends(Threading, _super);

    function Threading() {
      this.beforeAnnotationCreated = __bind(this.beforeAnnotationCreated, this);
      this.annotationsLoaded = __bind(this.annotationsLoaded, this);
      this.annotationDeleted = __bind(this.annotationDeleted, this);
      _ref = Threading.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    Threading.prototype.events = {
      'annotationDeleted': 'annotationDeleted',
      'annotationsLoaded': 'annotationsLoaded',
      'beforeAnnotationCreated': 'beforeAnnotationCreated'
    };

    Threading.prototype.cache = {};

    Threading.prototype.pluginInit = function() {
      return this.annotator.threading = mail.messageThread();
    };

    Threading.prototype.thread = function(annotation) {
      var prev, thread, _ref1;
      thread = this.annotator.threading.getContainer(annotation.id);
      thread.message = annotation;
      if ((_ref1 = annotation.references) != null ? _ref1.length : void 0) {
        prev = annotation.references[annotation.references.length - 1];
        this.annotator.threading.getContainer(prev).addChild(thread);
      }
      Object.defineProperty(annotation, 'thread', {
        configurable: true,
        enumerable: false,
        writable: true,
        value: thread
      });
      this.annotator.threading.idTable[annotation.id] = thread;
      return thread;
    };

    Threading.prototype.annotationDeleted = function(annotation) {
      var parent;
      parent = annotation.thread.parent;
      annotation.thread.message = null;
      delete this.annotator.threading.idTable[annotation.id];
      delete annotation.thread;
      if (parent != null) {
        return this.annotator.threading.pruneEmpties(parent);
      }
    };

    Threading.prototype.annotationsLoaded = function(annotations) {
      var a, _i, _len, _results;
      this.annotator.threading.thread(annotations);
      _results = [];
      for (_i = 0, _len = annotations.length; _i < _len; _i++) {
        a = annotations[_i];
        _results.push(this.thread(a));
      }
      return _results;
    };

    Threading.prototype.beforeAnnotationCreated = function(annotation) {
      Object.defineProperty(annotation, 'id', {
        configurable: true,
        enumerable: false,
        writable: true,
        value: window.btoa(Math.random())
      });
      return this.thread(annotation);
    };

    return Threading;

  })(Annotator.Plugin);

}).call(this);
