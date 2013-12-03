(function() {
  var SearchHelper, StreamSearch, get_quote,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  get_quote = function(annotation) {
    var quote, selector, target, _i, _j, _len, _len1, _ref, _ref1, _ref2;
    if (annotation.quote != null) {
      return annotation.quote;
    }
    if (_ref = !'target', __indexOf.call(annotation, _ref) >= 0) {
      return '';
    }
    quote = '(Reply annotation)';
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

  SearchHelper = (function() {
    function SearchHelper() {}

    SearchHelper.prototype.populateFilter = function(filter, models, rules, limit) {
      var and_or, case_sensitive, categories, category, catlist, es_query_string, exact_match, first, mapped_field, oper_part, rule, searchItem, val, val_list, value, value_part, values, _i, _j, _k, _l, _len, _len1, _len2, _len3, _ref;
      if (limit == null) {
        limit = 50;
      }
      categories = {};
      for (_i = 0, _len = models.length; _i < _len; _i++) {
        searchItem = models[_i];
        category = searchItem.attributes.category;
        value = searchItem.attributes.value;
        if (category === 'results') {
          limit = value;
        } else {
          if (category === 'text') {
            catlist = [];
            _ref = value.split(' ');
            for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
              val = _ref[_j];
              catlist.push(val);
            }
            categories[category] = catlist;
          } else {
            if (category in categories) {
              categories[category].push(value);
            } else {
              categories[category] = [value];
            }
          }
        }
      }
      filter.setPastDataHits(limit);
      for (category in categories) {
        values = categories[category];
        if (rules[category] == null) {
          continue;
        }
        if (!values.length) {
          continue;
        }
        rule = rules[category];
        exact_match = rule.exact_match != null ? rule.exact_match : true;
        case_sensitive = rule.case_sensitive != null ? rule.case_sensitive : false;
        and_or = rule.and_or != null ? rule.and_or : 'or';
        mapped_field = rule.path != null ? rule.path : '/' + category;
        es_query_string = rule.es_query_string != null ? rule.es_query_string : false;
        if (values.length === 1) {
          oper_part = rule.operator != null ? rule.operator : exact_match ? 'equals' : 'matches';
          value_part = rule.formatter ? rule.formatter(values[0]) : values[0];
          filter.addClause(mapped_field, oper_part, value_part, case_sensitive, es_query_string);
        } else {
          if (and_or === 'or') {
            val_list = '';
            first = true;
            for (_k = 0, _len2 = values.length; _k < _len2; _k++) {
              val = values[_k];
              if (!first) {
                val_list += ',';
              } else {
                first = false;
              }
              value_part = rule.formatter ? rule.formatter(val) : val;
              val_list += value_part;
            }
            oper_part = rule.operator != null ? rule.operator : exact_match ? 'one_of' : 'match_of';
            filter.addClause(mapped_field, oper_part, val_list, case_sensitive, es_query_string);
          } else {
            oper_part = rule.operator != null ? rule.operator : exact_match ? 'equals' : 'matches';
            for (_l = 0, _len3 = values.length; _l < _len3; _l++) {
              val = values[_l];
              value_part = rule.formatter ? rule.formatter(val) : val;
              filter.addClause(mapped_field, oper_part, value_part, case_sensitive, es_query_string);
            }
          }
        }
      }
      if (limit !== 50) {
        categories['results'] = [limit];
      }
      return [filter.getFilter(), categories];
    };

    return SearchHelper;

  })();

  StreamSearch = (function() {
    StreamSearch.prototype.facets = ['text', 'tags', 'uri', 'quote', 'since', 'user', 'results'];

    StreamSearch.prototype.rules = {
      user: {
        formatter: function(user) {
          return 'acct:' + user + '@' + window.location.hostname;
        },
        path: '/user',
        exact_match: true,
        case_sensitive: false,
        and_or: 'or'
      },
      text: {
        path: '/text',
        exact_match: false,
        case_sensitive: false,
        and_or: 'and'
      },
      tags: {
        path: '/tags',
        exact_match: false,
        case_sensitive: false,
        and_or: 'or'
      },
      quote: {
        path: "/quote",
        exact_match: false,
        case_sensitive: false,
        and_or: 'and'
      },
      uri: {
        formatter: function(uri) {
          uri = uri.toLowerCase();
          if (uri.match(/http:\/\//)) {
            uri = uri.substring(7);
          }
          if (uri.match(/https:\/\//)) {
            uri = uri.substring(8);
          }
          if (uri.match(/^www\./)) {
            uri = uri.substring(4);
          }
          return uri;
        },
        path: '/uri',
        exact_match: false,
        case_sensitive: false,
        es_query_string: true,
        and_or: 'or'
      },
      since: {
        formatter: function(past) {
          var seconds;
          seconds = (function() {
            switch (past) {
              case '5 min':
                return 5 * 60;
              case '30 min':
                return 30 * 60;
              case '1 hour':
                return 60 * 60;
              case '12 hours':
                return 12 * 60 * 60;
              case '1 day':
                return 24 * 60 * 60;
              case '1 week':
                return 7 * 24 * 60 * 60;
              case '1 month':
                return 30 * 24 * 60 * 60;
              case '1 year':
                return 365 * 24 * 60 * 60;
            }
          })();
          return new Date(new Date().valueOf() - seconds * 1000);
        },
        path: '/created',
        exact_match: false,
        case_sensitive: true,
        and_or: 'and',
        operator: 'ge'
      }
    };

    StreamSearch.inject = ['$element', '$location', '$scope', '$timeout', 'streamfilter'];

    function StreamSearch($element, $location, $scope, $timeout, streamfilter) {
      var buffer, param, params, search_query, value, values, _i, _len,
        _this = this;
      $scope.path = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + '/__streamer__';
      $scope.empty = false;
      buffer = new Array(16);
      uuid.v4(null, buffer, 0);
      this.clientID = uuid.unparse(buffer);
      $scope.sortAnnotations = function(a, b) {
        var a_upd, b_upd;
        a_upd = a.updated != null ? new Date(a.updated) : new Date();
        b_upd = b.updated != null ? new Date(b.updated) : new Date();
        return a_upd.getTime() - b_upd.getTime();
      };
      search_query = '';
      params = $location.search();
      for (param in params) {
        values = params[param];
        if (__indexOf.call(this.facets, param) >= 0) {
          if (!(values instanceof Array)) {
            values = [values];
          }
          for (_i = 0, _len = values.length; _i < _len; _i++) {
            value = values[_i];
            search_query += param + ': "' + value + '" ';
          }
        }
      }
      this.search = VS.init({
        container: $element.find('.visual-search'),
        query: search_query,
        callbacks: {
          search: function(query, searchCollection) {
            var filter, _ref;
            filter = streamfilter.setMatchPolicyIncludeAll().noClauses();
            _ref = new SearchHelper().populateFilter(filter, searchCollection.models, _this.rules), filter = _ref[0], $scope.categories = _ref[1];
            $scope.initStream(filter);
            return $location.search($scope.categories);
          },
          facetMatches: function(callback) {
            var add_created, add_limit, facet, list, _j, _len1, _ref;
            add_limit = true;
            add_created = true;
            _ref = _this.search.searchQuery.facets();
            for (_j = 0, _len1 = _ref.length; _j < _len1; _j++) {
              facet = _ref[_j];
              if (facet.hasOwnProperty('results')) {
                add_limit = false;
              }
              if (facet.hasOwnProperty('since')) {
                add_created = false;
              }
            }
            if (add_limit && add_created) {
              list = ['text', 'tags', 'uri', 'quote', 'since', 'user', 'results'];
            } else {
              if (add_limit) {
                list = ['text', 'tags', 'uri', 'quote', 'user', 'results'];
              } else {
                if (add_created) {
                  list = ['text', 'tags', 'uri', 'quote', 'since', 'user'];
                } else {
                  list = ['text', 'tags', 'uri', 'quote', 'user'];
                }
              }
            }
            return callback(list, {
              preserveOrder: true
            });
          },
          valueMatches: function(facet, searchTerm, callback) {
            switch (facet) {
              case 'results':
                return callback(['0', '10', '25', '50', '100', '250', '1000']);
              case 'since':
                return callback(['5 min', '30 min', '1 hour', '12 hours', '1 day', '1 week', '1 month', '1 year'], {
                  preserveOrder: true
                });
            }
          },
          clearSearch: function(original) {
            original();
            return $scope.$apply(function() {
              $scope.annotations = [];
              $scope.empty = false;
              return $location.search({});
            });
          }
        }
      });
      $scope.initStream = function(filter) {
        var _this = this;
        if ($scope.sock != null) {
          $scope.sock.close();
        }
        $scope.annotations = new Array();
        $scope.sock = new SockJS($scope.path);
        $scope.sock.onopen = function() {
          var sockmsg;
          sockmsg = {
            filter: filter,
            clientID: _this.clientID
          };
          return $scope.sock.send(JSON.stringify(sockmsg));
        };
        $scope.sock.onclose = function() {};
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
          if (data.length) {
            return $scope.$apply(function() {
              $scope.empty = false;
              return $scope.manage_new_data(data, action);
            });
          } else {
            if (!$scope.annotations.length) {
              return $scope.$apply(function() {
                return $scope.empty = true;
              });
            }
          }
        };
      };
      $scope.manage_new_data = function(data, action) {
        var ann, annotation, found, index, _j, _k, _l, _len1, _len2, _len3, _ref, _ref1;
        for (_j = 0, _len1 = data.length; _j < _len1; _j++) {
          annotation = data[_j];
          annotation.action = action;
          annotation.quote = get_quote(annotation);
          annotation._share_link = window.location.protocol + '//' + window.location.hostname + ':' + window.location.port + "/a/" + annotation.id;
          annotation._anim = 'fade';
          if (__indexOf.call($scope.annotations, annotation) >= 0) {
            continue;
          }
          switch (action) {
            case 'create':
            case 'past':
              if (__indexOf.call($scope.annotations, annotation) < 0) {
                $scope.annotations.unshift(annotation);
              }
              break;
            case 'update':
              index = 0;
              found = false;
              _ref = $scope.annotations;
              for (_k = 0, _len2 = _ref.length; _k < _len2; _k++) {
                ann = _ref[_k];
                if (ann.id === annotation.id) {
                  $scope.annotations.splice(index, 1);
                  $scope.annotations.unshift(annotation);
                  found = true;
                  break;
                }
                index += 1;
              }
              if (!found) {
                $scope.annotations.unshift(annotation);
              }
              break;
            case 'delete':
              index = 0;
              _ref1 = $scope.annotations;
              for (_l = 0, _len3 = _ref1.length; _l < _len3; _l++) {
                ann = _ref1[_l];
                if (ann.id === annotation.id) {
                  $scope.annotations.splice(index, 1);
                  break;
                }
                index += 1;
              }
          }
        }
        return $scope.annotations = $scope.annotations.sort($scope.sortAnnotations).reverse();
      };
      $scope.loadMore = function(number) {
        var sockmsg;
        console.log('loadMore');
        if ($scope.sock == null) {
          return;
        }
        sockmsg = {
          messageType: 'more_hits',
          clientID: _this.clientID,
          moreHits: number
        };
        return $scope.sock.send(JSON.stringify(sockmsg));
      };
      $scope.annotations = [];
      $timeout(function() {
        return _this.search.searchBox.app.options.callbacks.search(_this.search.searchBox.value(), _this.search.searchBox.app.searchQuery);
      }, 500);
    }

    return StreamSearch;

  })();

  angular.module('h.streamsearch', ['h.streamfilter', 'h.filters', 'h.directives', 'bootstrap']).controller('StreamSearchController', StreamSearch);

}).call(this);
