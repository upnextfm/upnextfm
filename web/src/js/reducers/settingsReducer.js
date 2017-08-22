import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Nav reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function settingsReducer(state = initialState.settings, action = {}) {
  switch (action.type) {
    case types.SETTINGS_ALL:
      return Object.assign({}, state, action.settings);
    default:
      return state;
  }
}
