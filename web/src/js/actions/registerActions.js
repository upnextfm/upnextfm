import * as types from 'actions/actionTypes';
import { authLoginComplete } from 'actions/authActions';
import Auth from 'api/Auth';

/**
 * @returns {{type: string}}
 */
export function registerToggleDialog() {
  return {
    type: types.REGISTER_TOGGLE_DIALOG
  };
}

/**
 * @returns {{type: string}}
 */
export function registerReset() {
  return {
    type: types.REGISTER_RESET
  };
}

/**
 * @returns {{type: string}}
 */
export function registerBegin() {
  return {
    type: types.REGISTER_BEGIN
  };
}

/**
 * @returns {{type: string}}
 */
export function registerError(error) {
  return {
    type: types.REGISTER_ERROR,
    error
  };
}

/**
 * @returns {{type: string}}
 */
export function registerComplete(resp) {
  return {
    type: types.REGISTER_COMPLETE,
    resp
  };
}

/**
 * @param {{username: string, email: string, password: string}} details
 * @returns {Function}
 */
export function register(details) {
  return (dispatch) => {
    dispatch(registerBegin());
    return Auth.register(details)
      .then((resp) => {
        dispatch(registerComplete(resp));
        dispatch(authLoginComplete(resp, details.username));
      })
      .catch((error) => {
        dispatch(registerError(error));
      });
  };
}