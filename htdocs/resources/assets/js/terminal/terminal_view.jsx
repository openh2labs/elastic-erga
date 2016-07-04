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
            <div className="event-list-container">
                <ul className="event-list">
                    {list}
                </ul>
            </div>
        );
    }
});

var Search = React.createClass({

    render: function () {
        return (
            <form className="form-inline">
                <div className="form-group">
                    <input type="text" className="form-control" id="search" placeholder="search" />
                </div>
                <button type="submit" className="btn btn-default">Submit</button>
            </form>
        )
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
        console.log('------> ',config)
        this.__model = model;
        this.__$element = $element;

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
        this.__model.loadTail().then((collection) => {
            this.update(collection);
        }).catch((error) => {
            console.log('data Failed to Load', error);
        });
    }

    update(collection) {
        ReactDOM.render(
            <div className="">
                <div className="terminal">
                    <Events model={collection} />
                </div>
                <Search />
            </div>
            , this.__$element);

        if (this._config.polling) {
            this.__$container.scrollTop = this.__$container.scrollHeight;
        }
    }

    get __$container()  {
        return this.__$element.getElementsByClassName("event-list-container")[0];
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

exports.create = function create( $element, model, config) {
    let dependencies = {
        clock : new ClockFactory({systemTimers:window}).create({name:"terminal", frequency:1})
    };
    return new TerminalView(dependencies, $element, model, config );
};

exports.__Class = TerminalView;
