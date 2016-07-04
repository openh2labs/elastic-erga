/*
*	This file is to store in one plce all definitive shared actions across the appliction.
*	Generally actions should be defined here. http://redux.js.org/docs/basics/Actions.html
*/

import axios from 'axios';
import _ from 'underscore';
import { LOAD_DATA } from './types';

const TERMINAL_URL = 'http://localhost:10080/api/v1/terminal';
let pollId,
		lastParams,
		dispatch;

function action(actionType, payload) {

	return {
		type: actionType,
		payload,
	};

}

export function getTerminalData(params = {}) {

	lastParams = params;
	const query = _.isEmpty(params) ? params : { params };

	axios.get(TERMINAL_URL, query)
		.then(response => {

			dispatch(action(LOAD_DATA, response.data));

		})
		.catch((error) => {

			console.log(error);

		});

}

export function startPollServer() {

	if (typeof pollId == 'undefined') {

		pollId = setInterval(() => getTerminalData(lastParams), 3000);

	}

}

export function stopPollServer() {

	if (typeof pollId != 'undefined') {

		clearInterval(pollId);
		pollId = undefined;

	}

}

export function init(storeDispatch) {

	dispatch = storeDispatch;
	startPollServer();

}
