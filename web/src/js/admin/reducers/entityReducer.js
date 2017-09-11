import * as types from '../actions/types';
import initialState from '../store/initialState';

/**
 * Entity reducer
 *
 * state = {
 *    data: {}
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
        data: action.data
      });
    default:
      return state;
  }
}
