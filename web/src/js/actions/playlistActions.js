import { dispatchPayload } from 'actions/dispatch';
import { userToggleLoginDialog } from 'actions/userActions';
import * as types from 'actions/actionTypes';

/**
 * @param {string} roomName
 * @returns {string}
 */
function videoChannel(roomName) {
  return `${types.CHAN_VIDEO}/${roomName}`;
}

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
      api.socket.subscribe(videoChannel(room.name), (uri, payload) => {
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
      api.socket.dispatch(videoChannel(room.name), 'append', [
        url
      ]);
    }
  };
}

/**
 * @param {number} videoID
 * @returns {Function}
 */
export function playlistRemove(videoID) {
  return (dispatch, getState, api) => {
    const room = subscribe(dispatch, getState);
    if (room && room.name !== '') {
      api.socket.dispatch(videoChannel(room.name), 'remove', [
        videoID
      ]);
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
      api.socket.dispatch(videoChannel(room.name), 'vote', [
        videoID,
        1 // or -1 to down vote
      ]);
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
      api.socket.dispatch(videoChannel(room.name), 'playNext', [
        videoID
      ]);
    }
  };
}
