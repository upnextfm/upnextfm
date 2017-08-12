import { combineReducers } from 'redux';
import auth from 'reducers/authReducer';
import room from 'reducers/roomReducer';
import nav from 'reducers/navReducer';

const rootReducer = combineReducers({
  auth,
  room,
  nav
});

export default rootReducer;
