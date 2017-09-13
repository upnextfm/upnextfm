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
 * @param {string} entityName
 * @param {number} id
 * @returns {Function}
 */
export function entityLoad(entityName, id) {
  return (dispatch) => {
    dispatch(uiLoading(true));

    const config = fetchConfig('GET');

    return fetch(`/admin/entity/${entityName}/${id}`, config)
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
 * @param {string} entityName
 * @param {number} id
 * @param {File} file
 * @returns {Function}
 */
export function entityUpload(entityName, id, file) {
  return () => {
    const form = new FormData();
    form.append('file', file);

    const config = fetchConfig('POST', form);
    delete config.headers['Content-Type'];

    return fetch(`/admin/entity/${entityName}/${id}`, config)
      .then(resp => resp.json());
  };
}

/**
 * @param {string} entityName
 * @param {number} id
 * @param {*} values
 * @returns {Function}
 */
export function entityUpdate(entityName, id, values) {
  return (dispatch) => {
    dispatch(uiLoading(true));

    const update = () => {
      const config = fetchConfig('POST', JSON.stringify(values));

      return fetch(`/admin/entity/${entityName}/${id}`, config)
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

    if (values.avatar !== undefined) {
      return dispatch(entityUpload(entityName, id, values.avatar))
        .then((data) => {
          delete values.avatar;
          values.info.avatarSm = data.avatarSm;
          values.info.avatarMd = data.avatarMd;
          values.info.avatarLg = data.avatarLg;
          return update();
        });
    } else if (values.thumb !== undefined) {
      return dispatch(entityUpload(entityName, id, values.thumb))
        .then((data) => {
          delete values.thumb;
          values.settings.thumbSm = data.thumbSm;
          values.settings.thumbMd = data.thumbMd;
          values.settings.thumbLg = data.thumbLg;
          return update();
        });
    }

    return update();
  };
}
