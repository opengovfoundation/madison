var config = require('./test-config').config;
config.multiCapabilities = require('open-sauce-browsers')('angular-select2');
exports.config = config;
