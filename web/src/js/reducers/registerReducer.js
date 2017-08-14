import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

export default function registerReducer(state = initialState.register, action = {}) {
  switch (action.type) {
    case types.REGISTER_TOGGLE_DIALOG:
      return Object.assign({}, state, {
        isDialogOpen: !state.isDialogOpen
      });
    case types.REGISTER_BEGIN:
      return Object.assign({}, state, {
        error:        '',
        isSubmitting: true,
        isRegistered: false
      });
    case types.REGISTER_COMPLETE:
      return Object.assign({}, state, {
        error:        '',
        isSubmitting: false,
        isRegistered: true
      });
    case types.REGISTER_ERROR:
      return Object.assign({}, state, {
        error:        action.error,
        isSubmitting: false,
        isRegistered: false
      });
    case types.REGISTER_RESET:
      return Object.assign({}, initialState.register);
    default: return state;
  }
}
