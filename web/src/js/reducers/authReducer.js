import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import Auth from 'api/Auth';

const is = Object.assign({}, initialState.auth);
is.isAuthenticated = Auth.isAuthenticated();

export default function authReducer(state = is, action = {}) {
  switch (action.type) {
    case types.AUTH_LOGIN_BEGIN:
      return Object.assign({}, state, {
        errorMessage:    '',
        isSubmitting:    true,
        isAuthenticated: false
      });
    case types.AUTH_LOGIN_COMPLETE:
      return Object.assign({}, state, {
        username:        action.username,
        errorMessage:    '',
        isSubmitting:    false,
        isAuthenticated: true
      });
    case types.AUTH_LOGIN_FAILURE:
      return Object.assign({}, state, {
        isSubmitting:    false,
        isAuthenticated: false,
        errorMessage:    action.message
      });
    case types.AUTH_LOGOUT_COMPLETE:
      return Object.assign({}, state, {
        errorMessage:    '',
        isSubmitting:    false,
        isAuthenticated: false
      });
    case types.AUTH_TOGGLE_LOGIN_DIALOG:
      return Object.assign({}, state, {
        isLoginDialogOpen: !state.isLoginDialogOpen
      });
    case types.AUTH_TOGGLE_REGISTER_DIALOG:
      return Object.assign({}, state, {
        isRegisterDialogOpen: !state.isRegisterDialogOpen
      });
    case types.AUTH_RESET:
      return Object.assign({}, is);
    default: return state;
  }
}
