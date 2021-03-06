"use strict";

class DomJsModule {
    constructor(dependencies, $elements) {
        this.$placeholder   = $elements.$placeholder;
        this.$container     = $elements.$container;
        this.params = null;
    }

    mount() {
        if(this.$placeholder.length) {
            this.params = this.$placeholder.data('params');
            console.log(this.$placeholder);
            this.$placeholder.replaceWith(this.$container);
            this.onMountSuccess(this.$container, this.params, this.$placeholder);
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