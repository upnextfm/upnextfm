import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function user(state, action) {
  return {
    ...state,

  };
  return Object.assign({}, state, {
    user: Object.assign({}, state.user,  action.settings)
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
      return {
        ...state,
        user:   Object.assign({}, state.user, action.settings.user),
        room:   Object.assign({}, state.room, action.settings.room),
        site:   Object.assign({}, state.site, action.settings.site),
        socket: Object.assign({}, state.socket, action.settings.socket)
      };
    case types.SETTINGS_USER:
      return user(state, action);
    case types.SETTINGS_SITE:
      return {
        ...state,
        site: Object.assign({}, state.site,  action.settings)
      };
    case types.SETTINGS_ROOM:
      return {
        ...state,
        room: Object.assign({}, state.room,  action.settings)
      };
    case types.SETTINGS_SOCKET:
      return {
        ...state,
        socket: Object.assign({}, state.socket,  action.settings)
      };
    default:
      return state;
  }
}
