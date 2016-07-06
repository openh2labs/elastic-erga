import { LOAD_DATA } from '../actions/types';

//adds / replaces the terminal data in redux
export default function (state = [], action) {

	if (action.type === LOAD_DATA) {

		state = action.payload;

	}

	return state;

}
