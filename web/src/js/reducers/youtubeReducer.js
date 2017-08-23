import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Youtube reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function youtubeReducer(state = initialState.youtube, action = {}) {
  switch (action.type) {
    default:
      return state;
  }
}
