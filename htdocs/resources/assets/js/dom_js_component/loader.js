"use strict";

class DomJsModule {
    constructor($, console, $placeholder, $container) {
        this.$placeholder = $placeholder
        this.$container = $container
        this.params = null;
    }

    mount($placeholder, $container) {
        if($placeholder.length) {
            let params = this.params = this.$placeholder.data('params');
            $placeholder.replaceWith($container);
            this.onMountSuccess($container, params, $placeholder);
        }
        else {
            this.onMountFailure("Couldn't mount on placeholder. Invalid $placeholder!");
        }
    }

    onMountSuccess(container, params, placeholder) {

    }

    onMountFailure(error) {
        console.log(error);
    }
}

module.exports = DomJsModule;