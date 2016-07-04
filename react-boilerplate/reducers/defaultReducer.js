import { LOAD_DATA } from '../actions/types';

export default function (state = [], action) {

	if (action.type === LOAD_DATA) {

		state = action.payload;

	}

	return state;

}
