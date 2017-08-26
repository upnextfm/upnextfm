import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Auth reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function authReducer(state = initialState.auth, action = {}) {
  switch (action.type) {
    case types.AUTH_USERNAME:
      return Object.assign({}, state, {
        username:        action.username,
        error:           '',
        isSubmitting:    false,
        isAuthenticated: action.username !== ''
      });
    case types.AUTH_LOGIN_BEGIN:
      return Object.assign({}, state, {
        error:           '',
        username:        '',
        isSubmitting:    true,
        isAuthenticated: false
      });
    case types.AUTH_LOGIN_FAILURE:
      return Object.assign({}, state, {
        isSubmitting:    false,
        isAuthenticated: false,
        username:        '',
        error:           action.error
      });
    case types.AUTH_RESET:
      return Object.assign({}, initialState.auth);
    default: return state;
  }
}
