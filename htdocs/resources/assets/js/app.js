
window.$ = window.jQuery = require('jquery');
require('./bloodhound/init');
require('bootstrap-less');



var App = (function(){
    "use strict";
    var ergaTerminalView = require('./erga_terminal/erga_terminal_view').create($, window.console)

})()