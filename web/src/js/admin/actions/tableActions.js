import * as types from './types';
import { uiLoading } from './uiActions';

/**
 * @param {string} path
 * @param {number} page
 * @returns {Function}
 */
export function tableLoad(path, page = 1) {
  return (dispatch) => {
    dispatch(uiLoading(true));

    const config = {
      method:      'GET',
      credentials: 'same-origin',
      headers:     {
        Accept: 'application/json'
      }
    };

    return fetch(`/admin/${path}/${page}`, config)
      .then((resp) => {
        dispatch(uiLoading(false));

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
        dispatch(uiLoading(false));
        console.error(error);
      });
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
