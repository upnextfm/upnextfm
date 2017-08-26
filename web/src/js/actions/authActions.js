import * as types from 'actions/actionTypes';
import { roomJoin } from 'actions/roomActions';

/**
 * @returns {{type: string}}
 */
export function authReset() {
  return {
    type: types.AUTH_RESET
  };
}

/**
 * @returns {{type: string}}
 */
export function authLoginBegin() {
  return {
    type: types.AUTH_LOGIN_BEGIN
  };
}

/**
 * @param {string} error
 * @returns {{type: string, message: *}}
 */
export function authLoginError(error) {
  return {
    type: types.AUTH_LOGIN_FAILURE,
    error
  };
}

/**
 * @param username
 * @returns {{type: string, username: *}}
 */
export function authUsername(username) {
  return {
    type: types.AUTH_USERNAME,
    username
  };
}

/**
 * @param {{username: string, password: string}} creds
 * @returns {Function}
 */
export function authLogin(creds) {
  return (dispatch, getState, api) => {
    dispatch(authLoginBegin());

    const username = encodeURIComponent(creds.username);
    const password = encodeURIComponent(creds.password);
    const config = {
      method:      'POST',
      body:        `_username=${username}&_password=${password}&_remember_me=1`,
      credentials: 'same-origin',
      headers:     {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    };

    return fetch('/login_check', config)
      .then((resp) => {
        dispatch(authUsername(creds.username));
        const room = getState().room;
        if (room.name !== '') {
          dispatch(roomJoin(room.name));
        }

        return resp;
      })
      .catch((error) => {
        dispatch(authLoginError(error));
      });
  };
}

/**
 * @returns {Function}
 */
export function authLogout() {
  return (dispatch) => {
    dispatch(authLoginBegin());
    return fetch('/logout', {
      credentials: 'same-origin'
    })
      .then((resp) => {
        dispatch(authUsername(''));
        return resp;
      })
      .catch((error) => {
        console.info(error);
        dispatch(authUsername(''));
      });
  };
}

