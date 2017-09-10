import * as types from './types';
import { SubmissionError } from 'redux-form';
import { uiLoading } from './uiActions';
import { fetchConfig } from './config';

/**
 * @returns {{type: ENTITY_UPDATING}}
 */
export function entityUpdating() {
  return {
    type: types.ENTITY_UPDATING
  };
}

/**
 * @param {string} entity
 * @param {number} id
 * @returns {Function}
 */
export function entityLoad(entity, id) {
  return (dispatch) => {
    dispatch(uiLoading(true));

    const config = fetchConfig('GET');

    return fetch(`/admin/entity/${entity}/${id}`, config)
      .then((resp) => {
        dispatch(uiLoading(false));

        if (!resp.ok) {
          throw new Error('Entity load failed.');
        }
        return resp.json();
      })
      .then((data) => {
        dispatch({
          type: types.ENTITY_LOAD,
          data
        });
      })
      .catch((error) => {
        console.error(error);
        dispatch(uiLoading(false));
      });
  };
}

/**
 * @param {string} entity
 * @param {number} id
 * @param {*} values
 * @returns {Function}
 */
export function entityUpdate(entity, id, values) {
  return (dispatch) => {
    dispatch(uiLoading(true));

    const config = fetchConfig('POST', JSON.stringify(values));

    return fetch(`/admin/entity/${entity}/${id}`, config)
      .then(resp => resp.json())
      .then((data) => {
        dispatch(uiLoading(false));

        if (data.error !== undefined) {
          throw new Error(data.error);
        } else if (data.validationErrors) {
          throw new SubmissionError(data.validationErrors);
        }
        Materialize.toast('Updated!', 4000);

        return data;
      });
  };
}
