import * as types from './types';

/**
 * @param {string} path
 * @param {number} page
 * @returns {Function}
 */
export function tableLoad(path, page = 1) {
  return (dispatch) => {
    dispatch(tableLoading());

    const config = {
      method:      'GET',
      credentials: 'same-origin',
      headers:     {
        Accept: 'application/json'
      }
    };

    return fetch(`/admin/${path}/${page}`, config)
      .then((resp) => {
        if (!resp.ok) {
          throw new Error('Table load failed.');
        }
        return resp.json();
      })
      .then((table) => {
        dispatch({
          type: types.TABLE_LOAD,
          table
        });
      })
      .catch((error) => {
        console.error(error);
      });
  };
}

/**
 * @returns {{type: TABLE_LOADING}}
 */
export function tableLoading() {
  return {
    type: types.TABLE_LOADING
  };
}

/**
 * @param {string} filter
 * @returns {{type: TABLE_CHANGE_FILTER, filter: string}}
 */
export function tableChangeFilter(filter) {
  return {
    type: types.TABLE_CHANGE_FILTER,
    filter
  };
}
