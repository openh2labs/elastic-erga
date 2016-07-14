import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, browserHistory } from 'react-router';
import { createStore, applyMiddleware, compose } from 'redux';
import reduxThunk from 'redux-thunk';

import reducer from './reducers';
import { init as initActions, getTerminalData } from './actions';

import App from './components/app';
import Index from './components/terminal/index';

const store = createStore(reducer, {},
	compose(applyMiddleware(reduxThunk),
	window.devToolsExtension ? window.devToolsExtension() : f => f));
let attachTo;
let terminalURL;

try {
    terminalURL = componentConfig.terminal.TERMINAL_URL
}catch(e){
    console.error(e);
    console.error('Warning: componentConfig.terminal.TERMINAL_URL not set. Please ensure PHP is serving the config correctly.');
}

try {
    attachTo = componentConfig.terminal.ATTACH_COMPONENT_TO;
}catch(e){
    console.error(e);
    console.error('Warning: componentConfig.terminal.ATTACH_COMPONENT_TO not set. Please ensure PHP is serving the config correctly.');
}

initActions(store.dispatch, terminalURL);
getTerminalData();

ReactDOM.render(
	<Provider store={store} >
		<Router history={browserHistory}>
			<Route path="/" component={App}>
				<Route path="terminal" component={Index} />
			</Route>
		</Router>
	</Provider>
  , document.querySelector(attachTo)
);
