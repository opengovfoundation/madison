(function() {
  var Displayer, get_quote,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  get_quote = function(annotation) {
    var quote, selector, target, _i, _j, _len, _len1, _ref, _ref1, _ref2;
    if (_ref = !'target', __indexOf.call(annotation, _ref) >= 0) {
      return '';
    }
    quote = '(This is a reply annotation)';
    _ref1 = annotation['target'];
    for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
      target = _ref1[_i];
      _ref2 = target['selector'];
      for (_j = 0, _len1 = _ref2.length; _j < _len1; _j++) {
        selector = _ref2[_j];
        if (selector['type'] === 'TextQuoteSelector') {
          quote = selector['exact'] + ' ';
        }
      }
    }
    return quote;
  };

  Displayer = (function() {
    Displayer.prototype.path = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/__streamer__';

    Displayer.prototype.idTable = {};

    Displayer.$inject = ['$scope', '$element', '$timeout', 'streamfilter'];

    function Displayer($scope, $element, $timeout, streamfilter) {
      var buffer,
        _this = this;
      buffer = new Array(16);
      uuid.v4(null, buffer, 0);
      this.clientID = uuid.unparse(buffer);
      $scope.root = document.init_annotation;
      $scope.annotation = $scope.root.annotation;
      $scope.annotations = [$scope.annotation];
      $scope.annotation.replies = [];
      $scope.annotation.reply_count = 0;
      $scope.annotation.ref_length = $scope.annotation.references != null ? $scope.annotation.references.length : 0;
      $scope.full_deleted = false;
      this.idTable[$scope.annotation.id] = $scope.annotation;
      $scope.filter = streamfilter.setPastDataNone().setMatchPolicyIncludeAny().addClausesParse('references:^' + $scope.annotation.id).addClausesParse('id:=' + $scope.annotation.id).getFilter();
      $scope.change_annotation_content = function(id, new_annotation) {
        var k, replies, reply_count, to_change, v;
        to_change = _this.idTable[id];
        replies = to_change.replies;
        reply_count = to_change.reply_count;
        for (k in to_change) {
          v = to_change[k];
          delete to_change.k;
        }
        angular.extend(to_change, new_annotation);
        to_change.replies = replies;
        return to_change.reply_count = reply_count;
      };
      $scope.open = function() {
        $scope.sock = new SockJS(_this.path);
        $scope.sock.onopen = function() {
          var sockmsg;
          sockmsg = {
            filter: $scope.filter,
            clientID: _this.clientID
          };
          return $scope.sock.send(JSON.stringify(sockmsg));
        };
        $scope.sock.onclose = function() {
          return $timeout($scope.open, 5000);
        };
        return $scope.sock.onmessage = function(msg) {
          var action, data;
          console.log('Got something');
          console.log(msg);
          if (!((msg.data.type != null) && msg.data.type === 'annotation-notification')) {
            return;
          }
          data = msg.data.payload;
          action = msg.data.options.action;
          if (!(data instanceof Array)) {
            data = [data];
          }
          return $scope.$apply(function() {
            return $scope.manage_new_data(data, action);
          });
        };
      };
      $scope.manage_new_data = function(data, action) {
        var annotation, i, pos, reference, replies, reply, _i, _j, _k, _l, _len, _len1, _ref, _ref1, _ref2, _ref3, _ref4, _results;
        data.sort(function(a, b) {
          if (a.created > b.created) {
            return 1;
          }
          if (a.created < b.created) {
            return -1;
          }
          return 0;
        });
        _results = [];
        for (_i = 0, _len = data.length; _i < _len; _i++) {
          annotation = data[_i];
          annotation.quote = get_quote(annotation);
          switch (action) {
            case 'create':
            case 'past':
              if (_ref = annotation.id, __indexOf.call(_this.idTable, _ref) >= 0) {
                break;
              }
              for (i = _j = _ref1 = $scope.annotation.ref_length, _ref2 = annotation.references.length - 1; _ref1 <= _ref2 ? _j <= _ref2 : _j >= _ref2; i = _ref1 <= _ref2 ? ++_j : --_j) {
                reference = annotation.references[i];
                _this.idTable[reference].reply_count += 1;
              }
              replies = _this.idTable[annotation.references[annotation.references.length - 1]].replies;
              pos = 0;
              for (_k = 0, _len1 = replies.length; _k < _len1; _k++) {
                reply = replies[_k];
                if (reply.updated < annotation.updated) {
                  break;
                }
                pos += 1;
              }
              annotation.replies = [];
              annotation.reply_count = 0;
              _this.idTable[annotation.id] = annotation;
              _results.push(replies.splice(pos, 0, annotation));
              break;
            case 'update':
              _results.push($scope.change_annotation_content(annotation.id, annotation));
              break;
            case 'delete':
              if (__indexOf.call(annotation, 'deleted') >= 0) {
                _results.push($scope.change_annotation_content(annotation.id, annotation));
              } else {
                if (_this.idTable[annotation.id] == null) {
                  break;
                }
                if ($scope.annotation.id === annotation.id) {
                  _results.push($scope.full_deleted = true);
                } else {
                  for (i = _l = _ref3 = $scope.annotation.ref_length, _ref4 = annotation.references.length - 1; _ref3 <= _ref4 ? _l <= _ref4 : _l >= _ref4; i = _ref3 <= _ref4 ? ++_l : --_l) {
                    reference = annotation.references[i];
                    _this.idTable[reference].reply_count -= 1;
                  }
                  replies = _this.idTable[annotation.references[annotation.references.length - 1]].replies;
                  pos = replies.indexOf(_this.idTable[annotation.id]);
                  replies.splice(pos, 1);
                  _results.push(delete _this.idTable[annotation.id]);
                }
              }
              break;
            default:
              _results.push(void 0);
          }
        }
        return _results;
      };
      if ($scope.annotation.referrers != null) {
        $scope.manage_new_data($scope.annotation.referrers, 'past');
      }
      document.init_annotation = null;
      $scope.open();
    }

    return Displayer;

  })();

  angular.module('h.displayer', ['h.streamfilter', 'h.filters', 'h.directives', 'bootstrap']).controller('DisplayerCtrl', Displayer);

}).call(this);
