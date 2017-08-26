import * as types from 'actions/actionTypes';
import { authUsername } from 'actions/authActions';
import { roomJoin } from 'actions/roomActions';

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
export function registerComplete() {
  return {
    type: types.REGISTER_COMPLETE
  };
}

/**
 * @param {{username: string, email: string, password: string}} details
 * @returns {Function}
 */
export function registerSubmit(details) {
  return (dispatch, getState) => {
    dispatch(registerBegin());

    const email          = encodeURIComponent(details.email);
    const username       = encodeURIComponent(details.username);
    const password       = encodeURIComponent(details.password);
    const emailField     = `fos_user_registration_form[email]=${email}`;
    const usernameField  = `fos_user_registration_form[username]=${username}`;
    const passwordField  = `fos_user_registration_form[plainPassword][first]=${password}`;
    const password2Field = `fos_user_registration_form[plainPassword][second]=${password}`;
    const config = {
      method:      'POST',
      body:        `${emailField}&${usernameField}&${passwordField}&${password2Field}`,
      credentials: 'same-origin',
      headers:     {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    };

    return fetch('/register/', config)
      .then((resp) => {
        if (!resp.ok) {
          throw Error(resp.error);
        }
        dispatch(registerComplete());
        dispatch(authUsername(details.username));

        const room = getState().room;
        if (room.name !== '') {
          dispatch(roomJoin(room.name));
        }

        return resp;
      })
      .catch((error) => {
        dispatch(registerError(error));
      });
  };
}
