import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Register reducer
 *
 * state = {
 *    error:        null,
 *    isRegistered: false,
 *    isSubmitting: false
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function registerReducer(state = initialState.register, action = {}) {
  switch (action.type) {
    case types.REGISTER_TOGGLE_DIALOG:
      return {
        ...state,
        isDialogOpen: !state.isDialogOpen
      };
    case types.REGISTER_BEGIN:
      return {
        ...state,
        error:        '',
        isSubmitting: true,
        isRegistered: false
      };
    case types.REGISTER_COMPLETE:
      return {
        ...state,
        error:        '',
        isSubmitting: false,
        isRegistered: true
      };
    case types.REGISTER_ERROR:
      return {
        ...state,
        error:        action.error,
        isSubmitting: false,
        isRegistered: false
      };
    case types.REGISTER_RESET:
      return Object.assign({}, initialState.register);
    default: return state;
  }
}
