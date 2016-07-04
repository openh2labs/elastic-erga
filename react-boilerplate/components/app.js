import React from 'react';

class App {

	render() {

		return (<div>{this.props.children}</div>);

	}

}

App.propTypes = {
	children: React.PropTypes.object,
};

export default App;
