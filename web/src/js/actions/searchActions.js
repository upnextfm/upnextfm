import * as types from 'actions/actionTypes';

/**
 * @returns {{type: string}}
 */
export function searchBegin() {
  return {
    type: types.SEARCH_BEGIN
  };
}

/**
 * @param {Error} error
 * @returns {{type: string, error: Error}}
 */
export function searchError(error) {
  return {
    type: types.SEARCH_ERROR,
    error
  };
}

/**
 * @param {Array} results
 * @returns {{type: string, results: Array}}
 */
export function searchComplete(results) {
  return {
    type: types.SEARCH_COMPLETE,
    results
  };
}

/**
 * @returns {{type: string}}
 */
export function searchClear() {
  return {
    type: types.SEARCH_CLEAR
  };
}

/**
 * @param {string} query
 * @returns {Function}
 */
export function search(query) {
  return (dispatch, getState, api) => {
    api.youtube.search(query)
      .then((results) => {
        dispatch(searchComplete(results));
      }).catch((error) => {
        dispatch(searchError(error));
      });
  };
}
