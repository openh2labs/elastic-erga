"use strict";

window.$ = window.jQuery = require('jquery');

//TODO Remove this when elastic search is available
require('jquery-mockjax')(window.$, window);
require('../../../tests/js/mocks/elastic_server_mock').create(window.$);

require('./bloodhound/init');
require('bootstrap-less');

window.elastic_erga_app = (function($){
    /* Terminal */
    require('./terminal/terminal_factory').create($, window.console);
})(window.jQuery);