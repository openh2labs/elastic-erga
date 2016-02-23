
window.$ = window.jQuery = require('jquery');
require('./bloodhound/init');
require('bootstrap-less');



window.elastic_erga_app = (function($){
    "use strict";
    var ergaTerminalView = require('./terminal/terminal_factory').create($, window.console);
})(window.jQuery);