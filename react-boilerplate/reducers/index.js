import { combineReducers } from 'redux';
//import { reducer as form } from 'redux-form';
import terminalReducer from './defaultReducer';

const rootReducer = combineReducers({ terminalReducer });

export default rootReducer;
