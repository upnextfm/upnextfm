import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Playlist reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function playlistReducer(state = initialState.playlist, action = {}) {
  switch (action.type) {
    case types.PLAYLIST_SUBSCRIBE:
      return Object.assign({}, state, {
        subscribed: true
      });
    case types.PLAYLIST_START:
      return Object.assign({}, state, {
        current: action.current
      });
    default:
      return state;
  }
}
