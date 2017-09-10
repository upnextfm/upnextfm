import * as types from '../actions/types';
import initialState from '../store/initialState';

/**
 * UI reducer
 *
 * state = {
 *    isLoading: false
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function uiReducer(state = initialState.ui, action = {}) {
  switch (action.type) {
    case types.UI_LOADING:
      return assign({}, state, {
        isLoading: action.isLoading
      });
    default:
      return state;
  }
}
