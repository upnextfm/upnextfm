import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Video reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function playerReducer(state = initialState.player, action = {}) {
  switch (action.type) {
    case types.PLAYER_READY:
      return Object.assign({}, state, {
        isMuted:  action.isMuted,
        duration: action.duration
      });
    case types.PLAYER_TIME:
      return Object.assign({}, state, {
        time: action.time
      });
    case types.PLAYER_DURATION:
      return Object.assign({}, state, {
        duration: action.duration
      });
    case types.PLAYER_STATUS:
      return Object.assign({}, state, {
        status: action.status
      });
    case types.PLAYER_TOGGLE_PLAY:
      return Object.assign({}, state, {
        status: state.status === 1 ? 2 : 1
      });
    case types.PLAYER_TOGGLE_MUTE:
      return Object.assign({}, state, {
        isMuted: !state.isMuted
      });
    default:
      return state;
  }
}
