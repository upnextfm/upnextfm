import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import Auth from 'api/Auth';

export default function registerReducer(state = initialState.register, action = {}) {
  switch (action.type) {
    case types.REGISTER_TOGGLE_DIALOG:
      return Object.assign({}, state, {
        isDialogOpen: !state.isDialogOpen
      });
    case types.REGISTER_RESET:
      return Object.assign({}, initialState.register);
    default: return state;
  }
}
