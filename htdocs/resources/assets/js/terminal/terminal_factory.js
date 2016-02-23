"use strict";

/**
 *
 */

//var view = require ('./terminal_view.jsx');
var DomJsModule = require('./../dom_js_component/loader');

class ErgaTerminal extends DomJsModule {
        constructor($, console, view, $placeholder, $container) {

            super($, console, $placeholder, $container);
            this.view = view || require ('./terminal_view.jsx');
            this.mount($placeholder, $container);
        }

        onMountSuccess(container, params, placeholder) {
            super.onMountSuccess(container, params, placeholder);
            container.addClass('terminal');
            this.view.create(this.$container.get(0));
        }

        toString() {
            return '(' + this.element + ')';
        }
}

exports.create = function create($, console) {
    return new ErgaTerminal( $,
        console,
        require ('./terminal_view.jsx'),
        $('meta[type="js-module"][name="terminal"]'),
        $('<div>Wait while loading!</div>'));
}