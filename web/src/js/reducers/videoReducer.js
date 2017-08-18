import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Video reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function videoReducer(state = initialState.video, action = {}) {
  switch (action.type) {
    default:
      return state;
  }
}
