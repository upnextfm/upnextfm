import { combineReducers } from 'redux';
import nav from 'reducers/navReducer';
import auth from 'reducers/authReducer';
import register from 'reducers/registerReducer';
import room from 'reducers/roomReducer';
import users from 'reducers/usersReducer';
import video from 'reducers/videoReducer';
import playlist from 'reducers/playlistReducer';

const rootReducer = combineReducers({
  nav,
  auth,
  register,
  room,
  users,
  video,
  playlist
});

export default rootReducer;
