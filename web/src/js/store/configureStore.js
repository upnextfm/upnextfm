import { createStore, applyMiddleware, compose } from 'redux';
import thunk from 'redux-thunk';
import * as api from 'api';
import rootReducer from '../reducers/rootReducer';

const composeEnhancers = (PRODUCTION || window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ === undefined)
  ? compose
  : window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__;

export default function configureStore() {
  return createStore(
    rootReducer,
    composeEnhancers(applyMiddleware(thunk.withExtraArgument(api)))
  );
}
