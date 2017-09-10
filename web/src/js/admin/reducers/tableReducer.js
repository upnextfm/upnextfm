import * as types from '../actions/types';
import initialState from '../store/initialState';

/**
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function load(state, action) {
  const table = assign({}, action.table);
  table.isLoading   = false;
  table.numPages    = parseInt(table.numPages, 10);
  table.currentPage = parseInt(table.currentPage, 10);

  return assign({}, state, table);
}

/**
 * Table reducer
 *
 * state = {
 *    filter:       '',
 *    numPages:     1,
 *    currentPage:  1,
 *    orderables:   [],
 *    currentOrder: {},
 *    columns:      {},
 *    rows:         []
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function tableReducer(state = initialState.table, action = {}) {
  switch (action.type) {
    case types.TABLE_LOAD:
      return load(state, action);
    case types.TABLE_CHANGE_FILTER:
      return assign({}, state, {
        filter: action.filter
      });
    default:
      return state;
  }
}
