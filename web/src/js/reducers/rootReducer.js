import { combineReducers } from 'redux';
import auth from 'reducers/authReducer';
import register from 'reducers/registerReducer';
import room from 'reducers/roomReducer';
import nav from 'reducers/navReducer';

const rootReducer = combineReducers({
  auth,
  register,
  room,
  nav
});

export default rootReducer;
