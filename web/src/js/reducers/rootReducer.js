import { combineReducers } from 'redux';
import layout from 'reducers/layoutReducer';
import auth from 'reducers/authReducer';
import register from 'reducers/registerReducer';
import settings from 'reducers/settingsReducer';
import room from 'reducers/roomReducer';
import pms from 'reducers/pmsReducer';
import users from 'reducers/usersReducer';
import player from 'reducers/playerReducer';
import playlist from 'reducers/playlistReducer';

const rootReducer = combineReducers({
  layout,
  auth,
  register,
  settings,
  room,
  pms,
  users,
  player,
  playlist
});

export default rootReducer;
