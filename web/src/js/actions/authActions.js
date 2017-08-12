import * as types from 'actions/actionTypes';
import Auth from 'api/Auth';

/**
 * @returns {{type: string}}
 */
export function authLoginBegin() {
  return {
    type: types.AUTH_LOGIN_BEGIN
  };
}

/**
 * @param {{token: string}} resp
 * @param {string} username
 * @returns {{type: string, token: *, username: string}}
 */
export function authLoginComplete(resp, username) {
  return {
    type:  types.AUTH_LOGIN_COMPLETE,
    token: resp.token,
    username
  };
}

/**
 * @param {string} message
 * @returns {{type: string, message: *}}
 */
export function authLoginError(message) {
  return {
    type: types.AUTH_LOGIN_FAILURE,
    message
  };
}

/**
 * @param {{username: string, password: string}} creds
 * @returns {Function}
 */
export function authLogin(creds) {
  return (dispatch) => {
    dispatch(authLoginBegin());
    return Auth.login(creds)
      .then((resp) => {
        dispatch(authLoginComplete(resp, creds.username));
      })
      .catch((error) => {
        dispatch(authLoginError(error));
      });
  };
}

/**
 * @returns {{type: string}}
 */
export function authLogoutBegin() {
  return {
    type: types.AUTH_LOGOUT_BEGIN
  };
}

/**
 * @returns {{type: string}}
 */
export function authLogoutComplete() {
  return {
    type: types.AUTH_LOGOUT_COMPLETE
  };
}

/**
 * @returns {Function}
 */
export function authLogout() {
  return (dispatch) => {
    dispatch(authLogoutBegin());
    Auth.logout();
    dispatch(authLogoutComplete());
  };
}

/**
 * @returns {{type: string}}
 */
export function authToggleLoginDialog() {
  return {
    type: types.AUTH_TOGGLE_LOGIN_DIALOG
  };
}

/**
 * @returns {{type: string}}
 */
export function authReset() {
  return {
    type: types.AUTH_RESET
  };
}
