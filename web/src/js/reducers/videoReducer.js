import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Video reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function videoReducer(state = initialState.video, action = {}) {
  switch (action.type) {
    case types.VIDEO_READY:
      return Object.assign({}, state, {
        isMuted: action.isMuted
      });
    case types.VIDEO_TOGGLE_MUTE:
      return Object.assign({}, state, {
        isMuted: !state.isMuted
      });
    default:
      return state;
  }
}
