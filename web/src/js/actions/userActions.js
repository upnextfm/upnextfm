import * as types from 'actions/actionTypes';
import { roomJoin } from 'actions/roomActions';

/**
 * @returns {{type: string}}
 */
export function userReset() {
  return {
    type: types.USER_RESET
  };
}

/**
 * @returns {{type: string}}
 */
export function userLoginBegin() {
  return {
    type: types.USER_LOGIN_BEGIN
  };
}

/**
 * @param {string} error
 * @returns {{type: string, message: *}}
 */
export function userLoginError(error) {
  return {
    type: types.USER_LOGIN_FAILURE,
    error
  };
}

/**
 * @param username
 * @returns {{type: string, username: *}}
 */
export function userUsername(username) {
  return {
    type: types.USER_USERNAME,
    username
  };
}

/**
 * @param {Array} roles
 * @returns {{type: string, roles: Array}}
 */
export function userRoles(roles) {
  return {
    type: types.USER_ROLES,
    roles
  };
}

/**
 * @param {{username: string, password: string}} creds
 * @returns {Function}
 */
export function userLogin(creds) {
  return (dispatch, getState, api) => {
    dispatch(userLoginBegin());

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
        dispatch(userUsername(creds.username));
        const room = getState().room;
        if (room.name !== '') {
          dispatch(roomJoin(room.name));
        }

        return resp;
      })
      .catch((error) => {
        dispatch(userLoginError(error));
      });
  };
}

/**
 * @returns {Function}
 */
export function userLogout() {
  return (dispatch) => {
    dispatch(userLoginBegin());
    return fetch('/logout', {
      credentials: 'same-origin'
    })
      .then((resp) => {
        dispatch(userUsername(''));
        return resp;
      })
      .catch((error) => {
        console.info(error);
        dispatch(userUsername(''));
      });
  };
}

