"use strict";

/**
 *
 */

var DomJsModule = require('./../dom_js_component/loader');

class ErgaTerminal extends DomJsModule {
        constructor(dependencies, $elements, mvvm) {

            super(dependencies, $elements);
            this.view = mvvm.view; //This is a factory
            this.model = new mvvm.model({http: dependencies.$});
            this.mount();
        }

        onMountSuccess($container, config, $placeholder) {
            super.onMountSuccess();

            //this.$container.addClass('terminal');
            if (config && config.config) {
                this.model.serviceUrl = config.config.endpoint;
            }

            this.mountComponent(this.$container.get(0));
        }

        mountComponent(mountingElement) {

            this.view.create(mountingElement, this.model, this.config);
            console.log('React Terminal Component Mounted', mountingElement, this.model)
        }

        toString() {
            return '(' + this.element + ')';
        }
}

exports.create = function create($, console) {
    let view = require('./terminal_view.jsx');
    let model = require('./../models/Events');

    let $placeholder = $('meta[type="js-module"][name="terminal"]');
    let $container = $('<div>Wait while loading!</div>');

    let dependencies = {$, console};
    let $elements = { $placeholder , $container };
    let mvvm = { view , model };

    return new ErgaTerminal(dependencies, $elements, mvvm );
}