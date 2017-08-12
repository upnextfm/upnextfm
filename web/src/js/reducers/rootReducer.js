import { combineReducers } from 'redux';
import auth from 'reducers/authReducer';
import nav from 'reducers/navReducer';

const rootReducer = combineReducers({
  auth,
  nav
});

export default rootReducer;
