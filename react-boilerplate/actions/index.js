import axios from 'axios';
import _ from 'underscore';
import { LOAD_DATA } from './types';

const TERMINAL_URL = 'http://localhost:10080/api/v1/terminal';
let dispatch;

export function init(storeDispatch) {
	dispatch = storeDispatch;
}

export function getTerminalData(params = {}) {
	let query = _.isEmpty(params) ? params : { params }; 
	axios.get(TERMINAL_URL, query)
		.then(response => {
			dispatch({type: LOAD_DATA, payload: response.data});
		})
		.catch(function (error) {
			console.log(error);
		});
}

 function action(actionType, payload) {
	return {
		type: actionType,
		payload: payload,
	};
}