import * as types from 'actions/actionTypes';
import { userToggleLoginDialog } from 'actions/userActions';
import { layoutErrorMessage } from 'actions/layoutActions';
import { playerTime } from 'actions/playerActions';

export function playlistSubscribe() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      api.socket.subscribe(`${types.CHAN_VIDEO}/${room.name}`, (uri, payload) => {
        switch (payload.cmd) {
          case types.CMD_VIDEO_VIDEOS:
            dispatch({
              type:   types.PLAYLIST_VIDEOS,
              videos: payload.videos
            });
            break;
          case types.CMD_VIDEO_START:
            dispatch({
              type:    types.PLAYLIST_START,
              start:   payload.start,
              current: payload.video
            });
            break;
          case types.CMD_VIDEO_STOP:
            dispatch({
              type: types.PLAYLIST_STOP
            });
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
export function playlistAppend(url) {
  return (dispatch, getState, api) => { // eslint-disable-line
    if (!getState().playlist.subscribed) {
      dispatch(playlistSubscribe());
    }

    const room = getState().room;
    if (room.name !== '') {
      if (!getState().user.isAuthenticated) {
        return dispatch(userToggleLoginDialog());
      }

      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_APPEND,
        url
      });
    }
  };
}

export function playlistRemove(videoID) {
  return (dispatch, getState, api) => { // eslint-disable-line
    if (!getState().playlist.subscribed) {
      dispatch(playlistSubscribe());
    }

    const room = getState().room;
    if (room.name !== '') {
      if (!getState().user.isAuthenticated) {
        return dispatch(userToggleLoginDialog());
      }

      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_REMOVE,
        videoID
      });
    }
  };
}

export function playlistPlayNext(videoID) {
  return (dispatch, getState, api) => { // eslint-disable-line
    if (!getState().playlist.subscribed) {
      dispatch(playlistSubscribe());
    }

    const room = getState().room;
    if (room.name !== '') {
      if (!getState().user.isAuthenticated) {
        return dispatch(userToggleLoginDialog());
      }

      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_PLAYNEXT,
        videoID
      });
    }
  };
}
