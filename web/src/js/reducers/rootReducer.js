import { combineReducers } from 'redux';
import auth from 'reducers/authReducer';

const rootReducer = combineReducers({
  auth
});

export default rootReducer;
