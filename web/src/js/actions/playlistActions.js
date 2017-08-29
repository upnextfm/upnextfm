import * as types from 'actions/actionTypes';
import { authToggleLoginDialog } from 'actions/authActions';
import { layoutErrorMessage } from 'actions/layoutActions';
import { playerTime } from 'actions/playerActions';

export function playlistSubscribe() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      api.socket.subscribe(`${types.CHAN_VIDEO}/${room.name}`, (uri, payload) => {
        switch (payload.cmd) {
          case types.CMD_VIDEO_START:
            dispatch({
              type:    types.PLAYLIST_START,
              current: payload.video
            });
            dispatch(playerTime(0));
            break;
          case types.CMD_ERROR:
            dispatch(layoutErrorMessage(payload.error));
            break;
          default:

            break;
        }
      });
      dispatch({
        type: types.PLAYLIST_SUBSCRIBE
      });
    }
  };
}

/**
 *
 * @param {string} url
 * @returns {Function}
 */
export function playlistPlay(url) {
  return (dispatch, getState, api) => { // eslint-disable-line
    if (!getState().playlist.subscribed) {
      dispatch(playlistSubscribe());
    }

    const room = getState().room;
    if (room.name !== '') {
      if (!getState().auth.isAuthenticated) {
        return dispatch(authToggleLoginDialog());
      }

      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_PLAY,
        url
      });
    }
  };
}
