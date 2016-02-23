"use strict";

/**
 *
 */

class ErgaTerminalView {
        constructor($, console) {

            this.$element = $(".erga-terminal-view");
            if(this.$element.length) {
                this.$element.append("Hello World, This is Terminal!");
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