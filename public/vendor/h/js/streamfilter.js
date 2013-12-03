(function() {
  var ClauseParser, StreamFilter,
    __indexOf = [].indexOf || function(item) { for (var i = 0, l = this.length; i < l; i++) { if (i in this && this[i] === item) return i; } return -1; };

  ClauseParser = (function() {
    function ClauseParser() {}

    ClauseParser.prototype.filter_fields = ['references', 'text', 'user', 'uri', 'id', 'tags', 'created', 'updated'];

    ClauseParser.prototype.operators = ['=', '=>', '>=', '<=', '=<', '>', '<', '[', '#', '^', '{'];

    ClauseParser.prototype.operator_mapping = {
      '=': 'equals',
      '>': 'gt',
      '<': 'lt',
      '=>': 'ge',
      '>=': 'ge',
      '=<': 'le',
      '<=': 'le',
      '[': 'one_of',
      '#': 'matches',
      '^': 'first_of',
      '{': 'match_of'
    };

    ClauseParser.prototype.insensitive_operator = 'i';

    ClauseParser.prototype.parse_clauses = function(clauses) {
      var bads, clause, field, oper, operator, operator_found, parts, rest, sensitive, structure, value, _i, _j, _len, _len1, _ref, _ref1;
      bads = [];
      structure = [];
      if (!clauses) {
        return;
      }
      clauses = clauses.split(' ');
      for (_i = 0, _len = clauses.length; _i < _len; _i++) {
        clause = clauses[_i];
        clause = clause.trim();
        if (clause.length < 1) {
          continue;
        }
        parts = clause.split(/:(.+)/);
        if (!(parts.length > 1)) {
          bads.push([clause, 'Filter clause is not well separated']);
          continue;
        }
        if (_ref = parts[0], __indexOf.call(this.filter_fields, _ref) < 0) {
          bads.push([clause, 'Unknown filter field']);
          continue;
        }
        field = parts[0];
        if (parts[1][0] === this.insensitive_operator) {
          sensitive = false;
          rest = parts[1].slice(1);
        } else {
          sensitive = true;
          rest = parts[1];
        }
        operator_found = false;
        _ref1 = this.operators;
        for (_j = 0, _len1 = _ref1.length; _j < _len1; _j++) {
          operator = _ref1[_j];
          if ((rest.indexOf(operator)) === 0) {
            oper = this.operator_mapping[operator];
            if (operator === '[') {
              value = rest.slice(operator.length).split(',');
            } else {
              value = rest.slice(operator.length);
            }
            operator_found = true;
            if (field === 'user') {
              value = 'acct:' + value + '@' + window.location.hostname;
            }
            break;
          }
        }
        if (!operator_found) {
          bads.push([clause, 'Unknown operator']);
          continue;
        }
        structure.push({
          'field': '/' + field,
          'operator': oper,
          'value': value,
          'case_sensitive': sensitive
        });
      }
      return [structure, bads];
    };

    return ClauseParser;

  })();

  StreamFilter = (function() {
    StreamFilter.prototype.strategies = ['include_any', 'include_all', 'exclude_any', 'exclude_all'];

    StreamFilter.prototype.past_modes = ['none', 'hits', 'time'];

    StreamFilter.prototype.filter = {
      match_policy: 'include_any',
      clauses: [],
      actions: {
        create: true,
        update: true,
        "delete": true
      },
      past_data: {
        load_past: "none"
      }
    };

    function StreamFilter() {
      this.parser = new ClauseParser();
    }

    StreamFilter.prototype.getFilter = function() {
      return this.filter;
    };

    StreamFilter.prototype.getPastData = function() {
      return this.filter.past_data;
    };

    StreamFilter.prototype.getMatchPolicy = function() {
      return this.filter.match_policy;
    };

    StreamFilter.prototype.getClauses = function() {
      return this.filter.clauses;
    };

    StreamFilter.prototype.getActions = function() {
      return this.filter.actions;
    };

    StreamFilter.prototype.getActionCreate = function() {
      return this.filter.actions.create;
    };

    StreamFilter.prototype.getActionUpdate = function() {
      return this.filter.actions.update;
    };

    StreamFilter.prototype.getActionDelete = function() {
      return this.filter.actions["delete"];
    };

    StreamFilter.prototype.setPastDataNone = function() {
      this.filter.past_data = {
        load_past: 'none'
      };
      return this;
    };

    StreamFilter.prototype.setPastDataHits = function(hits) {
      this.filter.past_data = {
        load_past: 'hits',
        hits: hits
      };
      return this;
    };

    StreamFilter.prototype.setPastDataTime = function(time) {
      this.filter.past_data = {
        load_past: 'hits',
        go_back: time
      };
      return this;
    };

    StreamFilter.prototype.setMatchPolicy = function(policy) {
      this.filter.match_policy = policy;
      return this;
    };

    StreamFilter.prototype.setMatchPolicyIncludeAny = function() {
      this.filter.match_policy = 'include_any';
      return this;
    };

    StreamFilter.prototype.setMatchPolicyIncludeAll = function() {
      this.filter.match_policy = 'include_all';
      return this;
    };

    StreamFilter.prototype.setMatchPolicyExcludeAny = function() {
      this.filter.match_policy = 'exclude_any';
      return this;
    };

    StreamFilter.prototype.setMatchPolicyExcludeAll = function() {
      this.filter.match_policy = 'exclude_all';
      return this;
    };

    StreamFilter.prototype.setActions = function(actions) {
      this.filter.actions = actions;
      return this;
    };

    StreamFilter.prototype.setActionCreate = function(action) {
      this.filter.actions.create = action;
      return this;
    };

    StreamFilter.prototype.setActionUpdate = function(action) {
      this.filter.actions.update = action;
      return this;
    };

    StreamFilter.prototype.setActionDelete = function(action) {
      this.filter.actions["delete"] = action;
      return this;
    };

    StreamFilter.prototype.noClauses = function() {
      this.filter.clauses = [];
      return this;
    };

    StreamFilter.prototype.addClause = function(clause) {
      this.filter.clauses.push(clause);
      return this;
    };

    StreamFilter.prototype.addClause = function(field, operator, value, case_sensitive, es_query_string) {
      if (case_sensitive == null) {
        case_sensitive = false;
      }
      if (es_query_string == null) {
        es_query_string = false;
      }
      this.filter.clauses.push({
        field: field,
        operator: operator,
        value: value,
        case_sensitive: case_sensitive,
        es_query_string: es_query_string
      });
      return this;
    };

    StreamFilter.prototype.setClausesParse = function(clauses_to_parse, error_checking) {
      var res, _ref;
      if (error_checking == null) {
        error_checking = false;
      }
      res = this.parser.parse_clauses(clauses_to_parse);
      if (res[1].length) {
        console.log("Errors while parsing clause:");
        console.log(res[1]);
      }
      if ((res != null) && (!error_checking) || (error_checking && ((_ref = res[1]) != null ? _ref.length : void 0) === 0)) {
        this.filter.clauses = res[0];
      }
      return this;
    };

    StreamFilter.prototype.addClausesParse = function(clauses_to_parse, error_checking) {
      var clause, res, _i, _len, _ref, _ref1;
      if (error_checking == null) {
        error_checking = false;
      }
      res = this.parser.parse_clauses(clauses_to_parse);
      if ((res != null) && (!error_checking) || (error_checking && ((_ref = res[1]) != null ? _ref.length : void 0) === 0)) {
        _ref1 = res[0];
        for (_i = 0, _len = _ref1.length; _i < _len; _i++) {
          clause = _ref1[_i];
          this.filter.clauses.push(clause);
        }
      }
      return this;
    };

    StreamFilter.prototype.resetFilter = function() {
      this.setMatchPolicyIncludeAny();
      this.setActionCreate(true);
      this.setActionUpdate(true);
      this.setActionDelete(true);
      this.setPastDataNone();
      this.noClauses();
      return this;
    };

    return StreamFilter;

  })();

  angular.module('h.streamfilter', ['bootstrap']).service('clauseparser', ClauseParser).service('streamfilter', StreamFilter);

}).call(this);
