"use strict";

var React = require('react');
var ReactDOM = require('react-dom');

var Event = React.createClass({
    render: function() {

        return <li className="event">
            <span className="timestamp">{this.props.model.timestamp}</span>
            <span className="description">{this.props.model.description}</span>
            <span className="service">{this.props.model.service}</span>
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