(function() {
  var Converter, elide, fuzzyTime, userName,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  Converter = (function(_super) {
    __extends(Converter, _super);

    function Converter() {
      Converter.__super__.constructor.apply(this, arguments);
      this.hooks.chain("preConversion", function(text) {
        if (text) {
          return text;
        } else {
          return "";
        }
      });
      this.hooks.chain("postConversion", function(text) {
        return text.replace(/<a href=/g, "<a target=\"_blank\" href=");
      });
    }

    return Converter;

  })(Markdown.Converter);

  fuzzyTime = function(date) {
    var day, delta, fuzzy, hour, minute, month, week, year;
    if (!date) {
      return '';
    }
    delta = Math.round((+(new Date) - new Date(date)) / 1000);
    minute = 60;
    hour = minute * 60;
    day = hour * 24;
    week = day * 7;
    month = day * 30;
    year = day * 365;
    if (delta < 30) {
      fuzzy = 'moments ago';
    } else if (delta < minute) {
      fuzzy = delta + ' seconds ago';
    } else if (delta < 2 * minute) {
      fuzzy = 'a minute ago';
    } else if (delta < hour) {
      fuzzy = Math.floor(delta / minute) + ' minutes ago';
    } else if (Math.floor(delta / hour) === 1) {
      fuzzy = '1 hour ago';
    } else if (delta < day) {
      fuzzy = Math.floor(delta / hour) + ' hours ago';
    } else if (delta < day * 2) {
      fuzzy = 'yesterday';
    } else if (delta < month) {
      fuzzy = Math.round(delta / day) + ' days ago';
    } else if (delta < year) {
      fuzzy = Math.round(delta / month) + ' months ago';
    } else {
      fuzzy = Math.round(delta / year) + ' years ago';
    }
    return fuzzy;
  };

  userName = function(user) {
    var _ref;
    return (_ref = user != null ? user.match(/^acct:([^@]+)/) : void 0) != null ? _ref[1] : void 0;
  };

  elide = function(text, split_length) {
    if (text.length > split_length) {
      text = text.substring(0, split_length);
      text = text + '\u2026';
    }
    return text;
  };

  angular.module('h.filters', []).filter('converter', function() {
    return (new Converter()).makeHtml;
  }).filter('fuzzyTime', function() {
    return fuzzyTime;
  }).filter('userName', function() {
    return userName;
  }).filter('elide', function() {
    return elide;
  });

}).call(this);
