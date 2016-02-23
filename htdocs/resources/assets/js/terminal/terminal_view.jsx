var React = require('react');
var ReactDOM = require('react-dom');

var HelloMessage = React.createClass({
    render: function() {

        console.log(this.props.name);
        return <div>Hello {this.props.name}</div>;
    }
});

exports.create = function create( $element ){
    ReactDOM.render(<HelloMessage name="World!" />, $element);
}