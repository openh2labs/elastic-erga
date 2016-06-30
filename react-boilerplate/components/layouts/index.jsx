import React, { Component } from 'react';
import { connect } from 'react-redux';
import styles from './styles.scss';

import { getTerminalData } from '../../actions';

let UIData, searchPhrase = '';

class Index extends Component {

	componentWillMount() {
		update();
	}

	componentWillUpdate(nextProps) {
		update(nextProps);
	}

	onKeyPress (event) {
		searchPhrase = document.querySelector('.search').value.trim();
		const terminalData = localFilter(this.props.terminalData, searchPhrase);
		update({terminalData});
		this.forceUpdate();
		console.log(event.keyCode);
		if (searchPhrase && (event.keyCode == 13 || event.type == 'click')) {
			getTerminalData({q: searchPhrase});
		}
	}

	render() {
		
		return (
			<div>
				<header></header>
				<section className="container">
					<div className="section-terminal">{UIData}</div>
				</section>
				<footer>
					<div className="table-row">
						<div className="search-container"><input className="search" type="text" onKeyDown={this.onKeyPress.bind(this)} /></div>
						<div className="button-container"><button className="primary-btn" onClick={this.onKeyPress.bind(this)}>Search</button></div>
					</div>
				</footer>
			</div>);

	}
}

function mapStateToProps(state) {
	return {
		terminalData: state.terminalReducer
	};
}

function update(nextProps) {

	if(typeof nextProps !== 'undefined' && nextProps.terminalData instanceof Array){
		const mappedData = nextProps.terminalData.map( (row, i) => {
			return <div key={i}>{row.message}</div>;
		});

		UIData = [...mappedData];
		console.log('UIData.length', UIData.length);
	}
}

let localFilter = (data, term) => {
	return data.filter((item) => {
		let regex = new RegExp(term, 'i');
		return regex.test(item.event_message);
	})
}

export default connect(mapStateToProps)(Index);