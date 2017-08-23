import * as types from 'actions/actionTypes';

export function playerReady(duration) {
  return (dispatch, getState, api) => {
    dispatch({
      type:    types.PLAYER_READY,
      isMuted: api.storage.getItem('player:isMuted', getState().player.isMuted),
      duration
    });
  };
}

export function playerTime(time) {
  return {
    type: types.PLAYER_TIME,
    time
  };
}

export function playerDuration(duration) {
  return {
    type: types.PLAYER_DURATION,
    duration
  };
}

export function playerStatus(status) {
  return {
    type: types.PLAYER_STATUS,
    status
  };
}

export function playerTogglePlay() {
  return {
    type: types.PLAYER_TOGGLE_PLAY
  };
}

export function playerToggleMute() {
  return (dispatch, getState, api) => {
    dispatch({
      type: types.PLAYER_TOGGLE_MUTE
    });
    api.storage.setItem('player:isMuted', getState().player.isMuted);
  };
}
