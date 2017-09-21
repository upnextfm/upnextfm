import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Player reducer
 *
 * state = {
 *    time:     0,
 *    duration: 0,
 *    status:   -1,
 *    isMuted:  false
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function playerReducer(state = initialState.player, action = {}) {
  switch (action.type) {
    case types.PLAYER_READY:
      return {
        ...state,
        isMuted:  action.isMuted,
        duration: action.duration
      };
    case types.PLAYER_TIME:
      return {
        ...state,
        time: action.time
      };
    case types.PLAYER_DURATION:
      return {
        ...state,
        duration: action.duration
      };
    case types.PLAYER_STATUS:
      return {
        ...state,
        status: action.status
      };
    case types.PLAYER_TOGGLE_PLAY:
      return {
        ...state,
        status: state.status === 1 ? 2 : 1
      };
    case types.PLAYER_TOGGLE_MUTE:
      return {
        ...state,
        isMuted: !state.isMuted
      };
    default:
      return state;
  }
}
