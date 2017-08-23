import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import Auth from 'api/Auth';

const is = Object.assign({}, initialState.auth);
is.isAuthenticated = Auth.isAuthenticated();

/**
 * Auth reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function authReducer(state = is, action = {}) {
  switch (action.type) {
    case types.AUTH_LOGIN_BEGIN:
      return Object.assign({}, state, {
        error:           '',
        isSubmitting:    true,
        isAuthenticated: false
      });
    case types.AUTH_LOGIN_COMPLETE:
      return Object.assign({}, state, {
        username:        action.username,
        error:           '',
        isSubmitting:    false,
        isAuthenticated: true
      });
    case types.AUTH_LOGIN_FAILURE:
      return Object.assign({}, state, {
        isSubmitting:    false,
        isAuthenticated: false,
        error:           action.error
      });
    case types.AUTH_LOGOUT_COMPLETE:
      return Object.assign({}, state, {
        error:           '',
        isSubmitting:    false,
        isAuthenticated: false
      });
    case types.AUTH_RESET:
      return Object.assign({}, is);
    default: return state;
  }
}
