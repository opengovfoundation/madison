/* -*- js -*- */
/*{literal}<![CDATA[*/
/*yepnope1.5.x|WTFPL-csillag-1fa7a3e0*/
(function(e,t,n){function r(e){return!e||"loaded"==e||"complete"==e||"uninitialized"==e}function i(e,n,i,o,c,l){var s,u,y=t.createElement("script");o=o||f.errorTimeout,y.src=e;for(u in i)y.setAttribute(u,i[u]);n=l?a:n||v,y.onreadystatechange=y.onload=function(){!s&&r(y.readyState)&&(s=1,n(),y.onload=y.onreadystatechange=null)},d(function(){s||(s=1,n(1))},o),w(),insPoint=h?h.parentNode:p.firstChild,c?y.onload():insPoint.insertBefore(y,h)}function o(e,n,r,i,o,c){var l,s=t.createElement("link");i=i||f.errorTimeout,n=c?a:n||v,s.href=e,s.rel="stylesheet",s.type="text/css";for(l in r)s.setAttribute(l,r[l]);o||(w(),insPoint=h?h.parentNode:p.firstChild,insPoint.insertBefore(s,h),d(n,0))}function a(){var e=m.shift();g=1,e?e.t?d(function(){("c"==e.t?f.injectCss:f.injectJs)(e.s,0,e.a,e.x,e.e,1)},0):(e(),a()):g=0}function c(e,n,i,o,c,l,s){function u(t){if(!y&&r(p.readyState)&&(b.r=y=1,!g&&a(),t)){"img"!=e&&d(function(){x.removeChild(p)},50);for(var i in L[n])L[n].hasOwnProperty(i)&&L[n][i].onload();p.onload=p.onreadystatechange=null}}s=s||f.errorTimeout;var p=t.createElement(e),y=0,v=0,b={t:i,s:n,e:c,a:l,x:s};1===L[n]&&(v=1,L[n]=[]),"object"==e?(p.data=n,p.setAttribute("type","text/css")):(p.src=n,p.type=e),p.width=p.height="0",p.onerror=p.onload=p.onreadystatechange=function(){u.call(this,v)},m.splice(o,0,b),"img"!=e&&(v||2===L[n]?(w(),x.insertBefore(p,j?null:h),d(u,s)):L[n].push(p))}function l(e,t,n,r,i){return g=0,t=t||"j",T(e)?c("c"==t?O:A,e,t,this.i++,n,r,i):(m.splice(this.i++,0,e),1==m.length&&a()),this}function s(){var e=f;return e.loader={load:l,i:0},e}var u,f,p=t.documentElement,d=e.setTimeout,h=t.getElementsByTagName("script")[0],y={}.toString,m=[],g=0,v=function(){},b="MozAppearance"in p.style,j=b&&!!t.createRange().compareNode,x=j?p:h?h.parentNode:p.firstChild,E=e.opera&&"[object Opera]"==y.call(e.opera),C=!!t.attachEvent&&!E,S="webkitAppearance"in p.style&&!("async"in t.createElement("script")),A=b?"object":C||S?"script":"img",O=C?"script":S?"img":A,P=Array.isArray||function(e){return"[object Array]"==y.call(e)},N=function(e){return Object(e)===e},T=function(e){return"string"==typeof e},k=function(e){return"[object Function]"==y.call(e)},w=function(){h&&h.parentNode||(h=t.getElementsByTagName("script")[0])},B=[],L={},J={timeout:function(e,t){return t.length&&(e.timeout=t[0]),e}};f=function(e){function t(e){var t,n,r,i=e.split("!"),o=B.length,a=i.pop(),c=i.length,l={url:a,origUrl:a,prefixes:i};for(n=0;c>n;n++)r=i[n].split("="),t=J[r.shift()],t&&(l=t(l,r));for(n=0;o>n;n++)l=B[n](l);return l}function r(e){var t=e.split("?")[0];return t.substr(t.lastIndexOf(".")+1)}function i(e,i,o,a,c){var l=t(e),u=l.autoCallback;return r(l.url),l.bypass?void 0:(i&&(i=k(i)?i:i[e]||i[a]||i[e.split("/").pop().split("?")[0]]),l.instead?l.instead(e,i,o,a,c):(L[l.url]&&l.reexecute!==!0?l.noexec=!0:L[l.url]=1,e&&o.load(l.url,l.forceCSS||!l.forceJS&&"css"==r(l.url)?"c":n,l.noexec,l.attrs,l.timeout),(k(i)||k(u))&&o.load(function(){s(),i&&i(l.origUrl,c,a),u&&u(l.origUrl,c,a),L[l.url]=2}),void 0))}function o(e,t){function n(e,n){if(""===e||e){if(T(e))n||(s=function(){var e=[].slice.call(arguments);u.apply(this,e),f()}),i(e,s,t,0,a);else if(N(e)){r=function(){var t,n=0;for(t in e)e.hasOwnProperty(t)&&n++;return n}();for(o in e)e.hasOwnProperty(o)&&(n||--r||(k(s)?s=function(){var e=[].slice.call(arguments);u.apply(this,e),f()}:s[o]=function(e){return function(){var t=[].slice.call(arguments);e&&e.apply(this,t),f()}}(u[o])),i(e[o],s,t,o,a))}}else!n&&f()}var r,o,a=!!e.test,c=a?e.yep:e.nope,l=e.load||e.both,s=e.callback||v,u=s,f=e.complete||v;n(c,!!l||!!e.complete),l&&n(l),!l&&!!e.complete&&n("")}var a,c,l=this.yepnope.loader;if(T(e))i(e,0,l,0);else if(P(e))for(a=0;e.length>a;a++)c=e[a],T(c)?i(c,0,l,0):P(c)?f(c):N(c)&&o(c,l);else N(e)&&o(e,l)},f.addPrefix=function(e,t){J[e]=t},f.addFilter=function(e){B.push(e)},f.errorTimeout=1e4,null==t.readyState&&t.addEventListener&&(t.readyState="loading",t.addEventListener("DOMContentLoaded",u=function(){t.removeEventListener("DOMContentLoaded",u,0),t.readyState="complete"},0)),e.yepnope=s(),e.yepnope.executeStack=a,e.yepnope.injectJs=i,e.yepnope.injectCss=o})(this,document);
yepnope.addFilter(function (resourceObj) {
  if (/\.js(\?\d+)?/.test(resourceObj.url)) {
    resourceObj.attrs = resourceObj.attrs || {};
    resourceObj.attrs["charset"] = "UTF-8";
  }
  return resourceObj;
});
yepnope([
  {
    test: typeof(window.annotator) !== 'undefined',
    nope: ["https://hypothes.is/assets/lib/d3.js?bee704b4", "https://hypothes.is/assets/lib/jquery-1.10.2.js?91515770", "https://hypothes.is/assets/lib/jquery.scrollintoview.js?42cd3e5e", "https://hypothes.is/assets/lib/jquery.ui.widget.js?bddbabe4", "https://hypothes.is/assets/lib/jquery.ui.autocomplete.js?9979e47b", "https://hypothes.is/assets/lib/jquery.ui.core.js?67e9965f", "https://hypothes.is/assets/lib/jquery.ui.widget.js?bddbabe4", "https://hypothes.is/assets/lib/jquery.ui.menu.js?d5c1cd9f", "https://hypothes.is/assets/lib/jquery.ui.position.js?fe73e7c3", "https://hypothes.is/assets/lib/jquery-ui-smoothness.css?c93cb50e", "https://hypothes.is/assets/lib/jquery.ui.effect.js?77ee3f3a", "https://hypothes.is/assets/lib/jquery.ui.effect-blind.js?607efd93", "https://hypothes.is/assets/lib/jquery.ui.effect-highlight.js?0da42fff", "https://hypothes.is/assets/lib/jquery.ui.effect-forecolor-highlight.js?7f3011ef", "https://hypothes.is/assets/lib/jschannel.js?2f510985", "https://hypothes.is/assets/lib/gettext.js?946e49b9", "https://hypothes.is/assets/locale/data.js?8b715a6c", "https://hypothes.is/assets/lib/annotator.js?10d1fd53", "https://hypothes.is/assets/js/plugin/bridge.js?58aeb101", "https://hypothes.is/assets/lib/annotator.document.js?a138e9f7", "https://hypothes.is/assets/js/plugin/heatmap.js?86733692", "https://hypothes.is/assets/lib/annotator.texthighlights.js?ebd66a5a", "https://hypothes.is/assets/js/dom_text_mapper.js?0f56ba7d", "https://hypothes.is/assets/lib/annotator.domtextmapper.js?76167e0b", "https://hypothes.is/assets/lib/annotator.textanchors.js?c5696e3e", "https://hypothes.is/assets/lib/diff_match_patch_uncompressed.js?8df9a29a", "https://hypothes.is/assets/js/text_match_engines.js?3e31bc37", "https://hypothes.is/assets/js/dom_text_matcher.js?39dc7d15", "https://hypothes.is/assets/lib/annotator.fuzzytextanchors.js?2444fdd3", "https://hypothes.is/assets/js/page_text_mapper_core.js?3cc7761d", "https://hypothes.is/assets/lib/annotator.pdf.js?8e2aef21", "https://hypothes.is/assets/js/plugin/toolbar.js?031a897c", "https://hypothes.is/assets/js/guest.js?06e663df", "https://hypothes.is/assets/js/host.js?744e08f5", "https://hypothes.is/assets/css/inject.css?7fba9354"],
    complete: function () {
      window.annotator = null;
      var app = "https://hypothes.is/app"
        , bootstrap = [
          'if (!window.annotator) {',
          '  var klass, options;',
          '  klass = "host" === "host" ? Annotator.Host : Annotator.Guest;',
          '  options = {"Heatmap": {"container": ".annotator-frame"}, "Toolbar": {"container": ".annotator-frame"}};',
          '  options["app"] = "' + app + '";',
          '  annotator = new klass(document.body, options);',
          '  window.Annotator.noConflict().$.noConflict(true);',
          '}'
        ].join('\n')
        , script = document.createElement('script')
        , first = document.getElementsByTagName('script')[0]
        , isGecko = ("MozAppearance" in document.documentElement.style)
        , isGeckoLTE18 = isGecko && !! document.createRange().compareNode
        , insBeforeObj = isGeckoLTE18 ? document.documentElement : first ? first.parentNode : document.documentElement.firstChild
        ;
      script.text = bootstrap;
      insBeforeObj.insertBefore(script, first);
    }
  },
  {
    test: window.requestAnimationFrame,
    nope: "https://hypothes.is/assets/lib/polyfills/raf.js?e9cfa896"
  }
]);
/*]]>{/literal}*/
