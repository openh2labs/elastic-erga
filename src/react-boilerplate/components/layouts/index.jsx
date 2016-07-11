import React, { Component } from 'react';
import { connect } from 'react-redux';
import styles from './styles.scss';

import { getTerminalData, startPollServer, stopPollServer } from '../../actions';

let UIData, 
	searchPhrase = '', 
	lastScrollPos = 0, 
	timer,
	terminalSection;

class Index extends Component {

	componentWillMount() {
		update();
	}

	componentWillUpdate(nextProps) {
		update(nextProps);
	}

	componentDidUpdate() {
		terminalSection = document.querySelector('.section-terminal');
		terminalSection.scrollTop = terminalSection.scrollHeight;
	}

	onKeyPress (event) {
		searchPhrase = document.querySelector('.search').value.trim();
		const terminalData = localFilter(this.props.terminalData, searchPhrase);

		update({terminalData});
		this.forceUpdate();

		if (event.keyCode == 13 || event.type == 'click') {
			getTerminalData({q: searchPhrase});
		}
	}

	onScroll (event) {
		const currentScrollPos = terminalSection.scrollTop;

		if (currentScrollPos > lastScrollPos) {
			startPollServer();
		}
		else if (currentScrollPos < lastScrollPos) {
			stopPollServer();
		}

		lastScrollPos = currentScrollPos;
	}

	render() {
		return (
			<div>
				<header></header>
				<section className="container">
					<div className="section-terminal" ref="section-terminal" onScroll={this.onScroll.bind(this)}>{UIData}</div>
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
	}
}

let localFilter = (data, term) => {
	return data.filter((item) => {
		let regex = new RegExp(term, 'i');
		return regex.test(item.event_message);
	})
}

export default connect(mapStateToProps)(Index);