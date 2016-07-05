/*
*	This file is to store in one plce all definitive shared actions across the appliction.
*	Generally actions should be defined here. http://redux.js.org/docs/basics/Actions.html
*/

import axios from 'axios';
import _ from 'underscore';
import { LOAD_DATA } from './types';


let pollId,
		lastParams,
		dispatch,
		terminal_api_endpoint;

//generic action creator for redux
function action(actionType, payload) {

	return {
		type: actionType,
		payload,
	};

}

//responsible for retrieving terminal data
export function getTerminalData(params = {}) {

	lastParams = params;
	const query = _.isEmpty(params) ? params : { params };

	axios.get(terminal_api_endpoint, query)
		.then(response => {
			//sends data to redux store
			dispatch(action(LOAD_DATA, response.data));

		})
		.catch((error) => {

			console.log(error);

		});

}

//polls server at set intervals
export function startPollServer() {

	if (typeof pollId == 'undefined') {

		pollId = setInterval(() => getTerminalData(lastParams), 3000);

	}

}

//stops polling the server
export function stopPollServer() {

	if (typeof pollId != 'undefined') {

		clearInterval(pollId);
		pollId = undefined;

	}

}

//setup of action dependency and endpoint
export function init(storeDispatch, endpoint) {

	dispatch = storeDispatch;
	terminal_api_endpoint = endpoint;
	startPollServer();

}
