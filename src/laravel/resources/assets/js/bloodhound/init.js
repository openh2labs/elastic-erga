/**
 * Bloodhound has some special issues with browserify,
 * this is a workaround for being able to load it smoothly.
 *
 * @type {*|exports|module.exports}
 */

window.typeahead = require("typeahead.js-browserify");
window.typeahead.loadjQueryPlugin();
window.Bloodhound = window.typeahead.Bloodhound;

/**
 * Require the form implementations
 */

require('./alert_form');
require('./librato_form');