import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Search reducer
 *
 * state = {
 *    results: []
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function searchReducer(state = initialState.search, action = {}) {
  switch (action.type) {
    case types.SEARCH_CLEAR:
      return {
        ...state,
        isSubmitting: false,
        term:         '',
        results:      [],
        error:        null
      };
    case types.SEARCH_TERM:
      return {
        ...state,
        term: action.term
      };
    case types.SEARCH_BEGIN:
      return {
        ...state,
        isSubmitting: true,
        results:      [],
        error:        null
      };
    case types.SEARCH_ERROR:
      return {
        ...state,
        isSubmitting: true,
        results:      [],
        error:        action.error
      };
    case types.SEARCH_COMPLETE:
      return {
        ...state,
        isSubmitting: false,
        results:      action.results,
        error:        null
      };
    default:
      return state;
  }
}
