import * as types from 'actions/actionTypes';

export function videoReady() {
  return (dispatch, getState, api) => {
    dispatch({
      type:    types.VIDEO_READY,
      isMuted: api.storage.getItem('video:isMuted', getState().video.isMuted)
    });
  };
}

export function videoToggleMute() {
  return (dispatch, getState, api) => {
    dispatch({
      type: types.VIDEO_TOGGLE_MUTE
    });
    api.storage.setItem('video:isMuted', getState().video.isMuted);
  };
}
