"use strict";

var React = require('react');
var ReactDOM = require('react-dom');
var ClockFactory = require('../clock/ClockFactory');

var Event = React.createClass({
    render: function() {

        /**
         * See models/Event.js
         *
         *
         set model(rawData) {

            this.id             = rawData.id;
            this.source_ip      = rawData.source_ip;
            this.program        = rawData.program;
            this.message        = rawData.message;
            this.event_message  = rawData.event_message;
            this.severity       = rawData.severity;
            this.facility       = rawData.facility;
            this.html_class     = rawData.html_class;
            this.timestamp      = rawData.received_at

            }

         */

        let model = this.props.model;

        let classes = "event";
        if (model.severity) {
            classes += " "+model.severity;
        }
        if (model.html_class) {
            classes += " "+model.html_class;
        }

        return <li className={classes}>
            <span className="source_ip">{model.source_ip}</span>
            <span className="program">{model.program}</span>
            <span className="hostname">{model.hostname}</span>
            <span className="event_message">{model.event_message}</span>
            <span className="timestamp">{model.timestamp}</span>
        </li>;
    }
});


var Events = React.createClass({
    render: function() {

        var list = this.props.model.map((event) => {
            return (
                <Event key={event.id} model={event} />
            );
        });
        return (
            <ul className="event-list">
                {list}
            </ul>
        );
    }
});

/**
 * elements prefixed with single underscore "_" are meant to be read only
 *
 * elements prefixed with double undersore "__" are meant to be private
 * and It is not suggested to be used by other projects.
 */
class TerminalView {
    constructor (d, $element, model, config) {

        this.__$element = $element;
        this.__model = model;

        /** never change config manually use __configure instead */
        this._config = {
            polling:true
        };
        this._clock = d.clock;

        this.__configure(config);
        this.load();

        this._clock.onTick(()=>{
          this.__onTick();
        });
        if (this._config.polling) {

            this._clock.start();
        }

    }

    load() {
        this.__model.load().then((collection) => {
            this.update(collection);
        }).catch((error) => {
            console.log('data Failed to Load', error);
        });
    }

    update(collection) {
        ReactDOM.render(<Events model={collection} />, this.__$element);
    }

    __configure(config) {
        if (config) {
            for (let key in config) {
                let changed = false;

                if (this._config[key] != config[key]) {
                    changed = changed ? changed : {};
                    changed[key] = {
                        old_value: this._config[key],
                        value:config[key]
                    };
                }

                this._config[key] = config[key];

                if (changed) {
                    this.__configObserveHandler(changed);
                }
            }
        }
    }

    __configObserveHandler(changed) {
        if (changed.polling) this.__onPollingChanged (changed.polling.value)
    }

    __onPollingChanged(value) {
        value ? this._clock.start() : this._clock.stop();
    }

    __onTick() {
        this.load();
    }

}

exports.create = function create( $element, model, config){
    let dependecies = {
        clock : new ClockFactory({systemTimers:window}).create({name:"terminal", frequency:1})
    }
    return new TerminalView(dependecies, $element, model, config );
}

exports.__Class = TerminalView;
