import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import Storage from 'api/Storage';

/**
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function user(state, action) {
  const user = Object.assign({}, state.user,  action.settings);
  Storage.setItem('settings:user:textColor', user.textColor);
  return Object.assign({}, state, {
    user
  });
}

/**
 * Settings reducer
 *
 * state = {
 *    user: {
 *      showNotices: true
 *    },
 *    site: {},
 *    room: {}
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function settingsReducer(state = initialState.settings, action = {}) {
  switch (action.type) {
    case types.SETTINGS_ALL:
      return Object.assign({}, state, {
        user:   Object.assign({}, state.user, action.settings.user),
        room:   Object.assign({}, state.room, action.settings.room),
        site:   Object.assign({}, state.site, action.settings.site),
        socket: Object.assign({}, state.socket, action.settings.socket)
      });
      return Object.assign({}, state, action.settings);
    case types.SETTINGS_USER:
      return user(state, action);
    case types.SETTINGS_SITE:
      return Object.assign({}, state, {
        site: Object.assign({}, state.site,  action.settings)
      });
    case types.SETTINGS_ROOM:
      return Object.assign({}, state, {
        room: Object.assign({}, state.room,  action.settings)
      });
    case types.SETTINGS_SOCKET:
      return Object.assign({}, state, {
        socket: Object.assign({}, state.socket,  action.settings)
      });
    default:
      return state;
  }
}
