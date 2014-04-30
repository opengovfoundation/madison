var fs = require('fs'),
	document = require('jsdom').jsdom('<html><head></head><body></body></html>'),
	window = document.parentWindow;

(function () {
	// read angular source into memory
	var src = require('fs').readFileSync(__dirname + '/lib/angular.min.js', 'utf8');

	// replace implicit references
	src = src.replace('angular.element(document)', 'window.angular.element(document)');
	src = src.split('(navigator.userAgent)').join('(window.navigator.userAgent)');
	src = src.split('angular.$$csp').join('window.angular.$$csp');

	(new Function('window', 'document', src))(window, document);
})();

module.exports = window.angular;
