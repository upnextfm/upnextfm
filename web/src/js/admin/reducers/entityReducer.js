import * as types from '../actions/types';
import initialState from '../store/initialState';

/**
 * Entity reducer
 *
 * state = {
 *    isLoading: false,
 *    data:      {}
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function entityReducer(state = initialState.entity, action = {}) {
  switch (action.type) {
    case types.ENTITY_LOAD:
      return assign({}, state, {
        isLoading: false,
        data:      action.data
      });
    case types.ENTITY_LOADING:
      return assign({}, state, {
        isLoading: true
      });
    default:
      return state;
  }
}
