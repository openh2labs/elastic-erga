"use strict";

var React = require('react');
var ReactDOM = require('react-dom');

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


class TerminalView {
    constructor ($element, model) {
        this.$element = $element;
        this.model = model;
        this.model.load().then((collection) => {
            console.log('dataLoaded', collection);
            this.update(collection);
        }).catch((error) => {
            console.log('data Failed to Load', error);
        });
    }

    update(collection) {
        ReactDOM.render(<Events model={collection} />, this.$element);
    }
}

exports.create = function create( $element, model ){
    return new TerminalView( $element, model );
}