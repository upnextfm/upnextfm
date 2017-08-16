import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { publish, subscribe, unsubscribe } from 'utils/socket';
import rootReducer from '../reducers/rootReducer';

export default function configureStore() {
  const thunkMiddleware = thunk.withExtraArgument({ publish, subscribe, unsubscribe });
  return createStore(rootReducer, applyMiddleware(thunkMiddleware));
}
