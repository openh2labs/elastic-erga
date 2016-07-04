"use strict";

class DomJsModule {
    constructor(dependencies, $elements) {
        this.$placeholder   = $elements.$placeholder;
        this.$container     = $elements.$container;
        this.config = null;
    }

    mount() {
        if(this.$placeholder.length) {
            this.config = this.$placeholder.data('parameters');
            console.log(this.$placeholder);
            this.$placeholder.replaceWith(this.$container);
            this.onMountSuccess(this.$container, this.config, this.$placeholder);
        }
        else {
            this.onMountFailure("Couldn't mount on placeholder. Invalid $placeholder!");
        }
    }

    onMountSuccess() {
        console.log('DOM JS module mounted successful', arguments);
    }

    onMountFailure(error) {
        console.log(error);
    }
}

module.exports = DomJsModule;