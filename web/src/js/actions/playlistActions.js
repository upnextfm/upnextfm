import { dispatchPayload } from 'actions/dispatch';
import { userToggleLoginDialog } from 'actions/userActions';
import * as types from 'actions/actionTypes';

/**
 *
 * @param {Function} dispatch
 * @param {Function} getState
 * @returns {boolean|*|settings.room|{}|room|{name, numNewMessages, users, messages}}
 */
function subscribe(dispatch, getState) {
  if (!getState().playlist.subscribed) {
    dispatch(playlistSubscribe());
  }

  const room = getState().room;
  if (room.name !== '') {
    if (!getState().user.isAuthenticated) {
      dispatch(userToggleLoginDialog());
      return null;
    }
  }

  return room;
}

/**
 * @returns {Function}
 */
export function playlistSubscribe() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      api.socket.subscribe(`${types.CHAN_VIDEO}/${room.name}`, (uri, payload) => {
        if (payload.dispatch !== undefined) {
          dispatchPayload(dispatch, payload);
        } else {
          console.error('Invalid payload');
        }
      });
      dispatch({
        type: types.PLAYLIST_SUBSCRIBE
      });
    }
  };
}

/**
 * @param {array} videos
 * @returns {{type: *, videos: *}}
 */
export function playlistVideos(videos) {
  return {
    type: types.PLAYLIST_VIDEOS,
    videos
  };
}

/**
 * @param {number} start
 * @param {*} current
 * @returns {{type: *, start: *, current: *}}
 */
export function playlistStart(start, current) {
  return {
    type: types.PLAYLIST_START,
    start,
    current
  };
}

/**
 * @returns {{type: *}}
 */
export function playlistStop() {
  return {
    type: types.PLAYLIST_STOP
  };
}

/**
 *
 * @param {string} url
 * @returns {Function}
 */
export function playlistAppend(url) {
  return (dispatch, getState, api) => { // eslint-disable-line
    const room = subscribe(dispatch, getState);
    if (room && room.name !== '') {
      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_APPEND,
        url
      });
    }
  };
}

/**
 * @param {number} videoID
 * @returns {Function}
 */
export function playlistRemove(videoID) {
  return (dispatch, getState, api) => { // eslint-disable-line
    const room = subscribe(dispatch, getState);
    if (room && room.name !== '') {
      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_REMOVE,
        videoID
      });
    }
  };
}

/**
 * @param {number} videoID
 * @returns {Function}
 */
export function playlistUpvote(videoID) {
  return (dispatch, getState, api) => { // eslint-disable-line
    const room = subscribe(dispatch, getState);
    if (room && room.name !== '') {
      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_UPVOTE,
        videoID
      });
    }
  };
}

/**
 * @param {number} videoID
 * @returns {Function}
 */
export function playlistPlayNext(videoID) {
  return (dispatch, getState, api) => { // eslint-disable-line
    const room = subscribe(dispatch, getState);
    if (room && room.name !== '') {
      api.socket.publish(`${types.CHAN_VIDEO}/${room.name}`, {
        cmd: types.CMD_VIDEO_PLAYNEXT,
        videoID
      });
    }
  };
}
