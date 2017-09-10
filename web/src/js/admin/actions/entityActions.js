import * as types from './types';

/**
 * @param {string} entity
 * @param {number} id
 * @returns {Function}
 */
export function entityLoad(entity, id) {
  return (dispatch) => {
    dispatch(entityLoading());

    const config = {
      method:      'GET',
      credentials: 'same-origin',
      headers:     {
        Accept: 'application/json'
      }
    };

    return fetch(`/admin/entity/${entity}/${id}`, config)
      .then((resp) => {
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
  return () => {
    const config = {
      method:      'POST',
      credentials: 'same-origin',
      headers:     {
        'Accept':       'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(values)
    };

    return fetch(`/admin/entity/${entity}/${id}`, config)
      .then((resp) => {
        if (!resp.ok) {
          throw new Error('Entity update failed.');
        }
        return resp.json();
      })
      .then((data) => {
        Materialize.toast('Updated!', 4000);
        return data;
      })
      .catch((error) => {
        console.error(error);
      });
  };
}

/**
 * @returns {{type: ENTITY_LOADING}}
 */
export function entityLoading() {
  return {
    type: types.ENTITY_LOADING
  };
}
