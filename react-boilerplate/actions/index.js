import axios from 'axios';
import _ from 'underscore';
import { LOAD_DATA } from './types';

const TERMINAL_URL = 'http://localhost:10080/api/v1/terminal';
let dispatch, 
	lastParams, 
	pollId;

let action = (actionType, payload) => {
	return {
		type: actionType,
		payload: payload,
	};
}

export function init(storeDispatch) {
	dispatch = storeDispatch;
	startPollServer();

}

export function getTerminalData(params = {}) {
	lastParams = params;
	let query = _.isEmpty(params) ? params : { params }; 
	
	axios.get(TERMINAL_URL, query)
		.then(response => {
			dispatch(action(LOAD_DATA, response.data));
		})
		.catch(function (error) {
			console.log(error);
		});
}

export function startPollServer() {
	if(typeof pollId == 'undefined'){
		console.log('start polling server...');
		pollId = setInterval( () => getTerminalData(lastParams), 3000 );
	}
}

export function stopPollServer() {
	if(typeof pollId != 'undefined'){
		clearInterval( pollId );
		pollId = undefined;
		console.log('stopped polling server.');
	}
}