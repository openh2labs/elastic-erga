import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { Router, Route, IndexRoute, browserHistory } from 'react-router';
import { createStore, applyMiddleware, compose } from 'redux';
import reduxThunk from 'redux-thunk';

import reducer from './reducers';
import { init as initActions, getTerminalData } from './actions';

import App from './components/app';
import Index from './components/layouts/index';

const store = createStore(reducer, {},
	compose(applyMiddleware(reduxThunk),
	window.devToolsExtension ? window.devToolsExtension() : f => f));

const TERMINAL_URL = 'http://localhost:10080/api/v1/terminal';

initActions(store.dispatch, TERMINAL_URL);
getTerminalData();

ReactDOM.render(
	<Provider store={store} >
		<Router history={browserHistory}>
			<Route path="/" component={App}>
				<Route path="*" component={Index} />
			</Route>
		</Router>
	</Provider>
  , document.querySelector('.terminal')
);
