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
    default:
      return state;
  }
}
