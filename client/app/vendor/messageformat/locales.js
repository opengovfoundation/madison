MessageFormat.locale.af=function(n){return n===1?"one":"other"}
MessageFormat.locale.am=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.ar = function(n) {
  if (n === 0) {
    return 'zero';
  }
  if (n == 1) {
    return 'one';
  }
  if (n == 2) {
    return 'two';
  }
  if ((n % 100) >= 3 && (n % 100) <= 10 && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 100) >= 11 && (n % 100) <= 99 && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.bg=function(n){return n===1?"one":"other"}
MessageFormat.locale.bn=function(n){return n===1?"one":"other"}
MessageFormat.locale.br = function (n) {
  if (n === 0) {
    return 'zero';
  }
  if (n == 1) {
    return 'one';
  }
  if (n == 2) {
    return 'two';
  }
  if (n == 3) {
    return 'few';
  }
  if (n == 6) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.ca=function(n){return n===1?"one":"other"}
MessageFormat.locale.cs = function (n) {
  if (n == 1) {
    return 'one';
  }
  if (n == 2 || n == 3 || n == 4) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.cy = function (n) {
  if (n === 0) {
    return 'zero';
  }
  if (n == 1) {
    return 'one';
  }
  if (n == 2) {
    return 'two';
  }
  if (n == 3) {
    return 'few';
  }
  if (n == 6) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.da=function(n){return n===1?"one":"other"}
MessageFormat.locale.de=function(n){return n===1?"one":"other"}
MessageFormat.locale.el=function(n){return n===1?"one":"other"}
MessageFormat.locale.en=function(n){return n===1?"one":"other"}
MessageFormat.locale.es=function(n){return n===1?"one":"other"}
MessageFormat.locale.et=function(n){return n===1?"one":"other"}
MessageFormat.locale.eu=function(n){return n===1?"one":"other"}
MessageFormat.locale.fa=function(n){return "other"}
MessageFormat.locale.fi=function(n){return n===1?"one":"other"}
MessageFormat.locale.fil=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.fr=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.ga=function(n){return n==1?"one":(n==2?"two":"other")}
MessageFormat.locale.gl=function(n){return n===1?"one":"other"}
MessageFormat.locale.gsw=function(n){return n===1?"one":"other"}
MessageFormat.locale.gu=function(n){return n===1?"one":"other"}
MessageFormat.locale.he=function(n){return n===1?"one":"other"}
MessageFormat.locale.hi=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.hr = function (n) {
  if ((n % 10) == 1 && (n % 100) != 11) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 4 &&
      ((n % 100) < 12 || (n % 100) > 14) && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 10) === 0 || ((n % 10) >= 5 && (n % 10) <= 9) ||
      ((n % 100) >= 11 && (n % 100) <= 14) && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.hu=function(n){return "other"}
MessageFormat.locale.id=function(n){return "other"}
MessageFormat.locale["in"]=function(n){return "other"}
MessageFormat.locale.is=function(n){return n===1?"one":"other"}
MessageFormat.locale.it=function(n){return n===1?"one":"other"}
MessageFormat.locale.iw=function(n){return n===1?"one":"other"}
MessageFormat.locale.ja=function(n){return "other"}
MessageFormat.locale.kn=function(n){return "other"}
MessageFormat.locale.ko=function(n){return "other"}
MessageFormat.locale.lag = function (n) {
  if (n === 0) {
    return 'zero';
  }
  if (n > 0 && n < 2) {
    return 'one';
  }
  return 'other';
};
MessageFormat.locale.ln=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.lt = function (n) {
  if ((n % 10) == 1 && ((n % 100) < 11 || (n % 100) > 19)) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 9 &&
      ((n % 100) < 11 || (n % 100) > 19) && n == Math.floor(n)) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.lv = function (n) {
  if (n === 0) {
    return 'zero';
  }
  if ((n % 10) == 1 && (n % 100) != 11) {
    return 'one';
  }
  return 'other';
};
MessageFormat.locale.mk=function(n){return (n%10)==1&&n!=11?"one":"other"}
MessageFormat.locale.ml=function(n){return n===1?"one":"other"}
MessageFormat.locale.mo = function (n) {
  if (n == 1) {
    return 'one';
  }
  if (n === 0 || n != 1 && (n % 100) >= 1 &&
      (n % 100) <= 19 && n == Math.floor(n)) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.mr=function(n){return n===1?"one":"other"}
MessageFormat.locale.ms=function(n){return "other"}
MessageFormat.locale.mt = function (n) {
  if (n == 1) {
    return 'one';
  }
  if (n === 0 || ((n % 100) >= 2 && (n % 100) <= 4 && n == Math.floor(n))) {
    return 'few';
  }
  if ((n % 100) >= 11 && (n % 100) <= 19 && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.nl=function(n){return n===1?"one":"other"}
MessageFormat.locale.no=function(n){return n===1?"one":"other"}
MessageFormat.locale.or=function(n){return n===1?"one":"other"}
MessageFormat.locale.pl = function (n) {
  if (n == 1) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 4 &&
      ((n % 100) < 12 || (n % 100) > 14) && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 10) === 0 || n != 1 && (n % 10) == 1 ||
      ((n % 10) >= 5 && (n % 10) <= 9 || (n % 100) >= 12 && (n % 100) <= 14) &&
      n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.pt=function(n){return n===1?"one":"other"}
MessageFormat.locale.ro = function (n) {
  if (n == 1) {
    return 'one';
  }
  if (n === 0 || n != 1 && (n % 100) >= 1 &&
      (n % 100) <= 19 && n == Math.floor(n)) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.ru = function (n) {
  if ((n % 10) == 1 && (n % 100) != 11) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 4 &&
      ((n % 100) < 12 || (n % 100) > 14) && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 10) === 0 || ((n % 10) >= 5 && (n % 10) <= 9) ||
      ((n % 100) >= 11 && (n % 100) <= 14) && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.shi = function(n) {
  if (n >= 0 && n <= 1) {
    return 'one';
  }
  if (n >= 2 && n <= 10 && n == Math.floor(n)) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.sk = function (n) {
  if (n == 1) {
    return 'one';
  }
  if (n == 2 || n == 3 || n == 4) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.sl = function (n) {
  if ((n % 100) == 1) {
    return 'one';
  }
  if ((n % 100) == 2) {
    return 'two';
  }
  if ((n % 100) == 3 || (n % 100) == 4) {
    return 'few';
  }
  return 'other';
};
MessageFormat.locale.sq=function(n){return n===1?"one":"other"}
MessageFormat.locale.sr = function (n) {
  if ((n % 10) == 1 && (n % 100) != 11) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 4 &&
      ((n % 100) < 12 || (n % 100) > 14) && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 10) === 0 || ((n % 10) >= 5 && (n % 10) <= 9) ||
      ((n % 100) >= 11 && (n % 100) <= 14) && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.sv=function(n){return n===1?"one":"other"}
MessageFormat.locale.sw=function(n){return n===1?"one":"other"}
MessageFormat.locale.ta=function(n){return n===1?"one":"other"}
MessageFormat.locale.te=function(n){return n===1?"one":"other"}
MessageFormat.locale.th=function(n){return "other"}
MessageFormat.locale.tl=function(n){return n===0||n==1?"one":"other"}
MessageFormat.locale.tr=function(n){return "other"}
MessageFormat.locale.uk = function (n) {
  if ((n % 10) == 1 && (n % 100) != 11) {
    return 'one';
  }
  if ((n % 10) >= 2 && (n % 10) <= 4 &&
      ((n % 100) < 12 || (n % 100) > 14) && n == Math.floor(n)) {
    return 'few';
  }
  if ((n % 10) === 0 || ((n % 10) >= 5 && (n % 10) <= 9) ||
      ((n % 100) >= 11 && (n % 100) <= 14) && n == Math.floor(n)) {
    return 'many';
  }
  return 'other';
};
MessageFormat.locale.ur=function(n){return n===1?"one":"other"}
MessageFormat.locale.vi=function(n){return "other"}
MessageFormat.locale.zh=function(n){return "other"}
