import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Starts playing a video
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function start(state, action) {
  const current = Object.assign({}, action.current);
  current.start = action.start;

  return Object.assign({}, state, {
    current
  });
}

/**
 * Playlist reducer
 *
 * state = {
 *    current: {},
 *    videos:  [
 *        {
 *          codename:  'dDxgSvJINlU',
 *          permalink: 'https://youtu.be/dDxgSvJINlU',
 *          provider:  'youtube',
 *          seconds:   250,
 *          thumbnail: 'https://i.ytimg.com/vi/dDxgSvJINlU/mqdefault.jpg',
 *          title:     'Blue October - Hate Me'
 *        }
 *    ]
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function playlistReducer(state = initialState.playlist, action = {}) {
  switch (action.type) {
    case types.PLAYLIST_SUBSCRIBE:
      return Object.assign({}, state, {
        subscribed: true
      });
    case types.PLAYLIST_VIDEOS:
      return Object.assign({}, state, {
        videos: action.videos
      });
    case types.PLAYLIST_START:
      return start(state, action);
    case types.PLAYLIST_STOP:
      return Object.assign({}, state, {
        current: {}
      });
    default:
      return state;
  }
}
