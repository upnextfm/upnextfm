import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Nav reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function navReducer(state = initialState.nav, action = {}) {
  switch (action.type) {
    case types.NAV_TOGGLE_DRAWER:
      return Object.assign({}, state, {
        isDrawerOpen: !state.isDrawerOpen
      });
    default:
      return state;
  }
}
