import * as types from '../actions/types';
import initialState from '../store/initialState';

/**
 * Table reducer
 *
 * state = {
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function tableReducer(state = initialState.table, action = {}) {
  switch (action.type) {
    case types.TABLE_LOAD:
      action.table.isLoading = false;
      return assign({}, state, action.table);
    case types.TABLE_LOADING:
      return assign({}, state, {
        isLoading: true
      });
    case types.TABLE_CHANGE_FILTER:
      return assign({}, state, {
        filter: action.filter
      });
    default:
      return state;
  }
}
