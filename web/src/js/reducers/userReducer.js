import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * User reducer
 *
 * state = {
 *    username:        '',
 *    roles:           [],
 *    error:           null,
 *    isAuthenticated: false,
 *    isSubmitting:    false
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function userReducer(state = initialState.user, action = {}) {
  switch (action.type) {
    case types.USER_USERNAME:
      return {
        ...state,
        username:        action.username,
        error:           '',
        isSubmitting:    false,
        isAuthenticated: action.username !== ''
      };
    case types.USER_ROLES:
      return {
        ...state,
        roles: action.roles
      };
    case types.USER_LOGIN_BEGIN:
      return {
        ...state,
        error:           '',
        username:        '',
        isSubmitting:    true,
        isAuthenticated: false
      };
    case types.USER_LOGIN_FAILURE:
      return {
        ...state,
        isSubmitting:    false,
        isAuthenticated: false,
        username:        '',
        error:           action.error
      };
    case types.USER_RESET:
      return Object.assign({}, initialState.user);
    default: return state;
  }
}
