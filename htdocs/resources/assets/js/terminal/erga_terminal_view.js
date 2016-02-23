"use strict";

/**
 *
 */

var view = require ('./terminal_view.jsx');

class ErgaTerminalView {
        constructor($, console) {

            this.$placeholder = $('meta[type="js-module"][name="terminal"]');
            this.$container = $('<div>Wait while loading!</div>');

            if(this.$placeholder.length) {

                this.$placeholder
                    .replaceWith(this.$container)
                    .addClass('terminal');

                view.create(this.$container.get(0));
            }
            else {
                console.log("Beware the lack of terminal view element");
            }
        }


        toString() {
            return '(' + this.element + ')';
        }
}

exports.create = function create(a, b) {
    return new ErgaTerminalView(a, b);
}