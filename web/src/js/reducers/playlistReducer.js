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

  return { ...state, current };
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
      return {
        ...state,
        subscribed: true
      };
    case types.PLAYLIST_VIDEOS:
      return {
        ...state,
        videos: action.videos
      };
    case types.PLAYLIST_STOP:
      return {
        ...state,
        current: {}
      };
    case types.PLAYLIST_TIME_UPDATE:
      console.log(action);
    case types.PLAYLIST_START:
      return start(state, action);
    default:
      return state;
  }
}
