import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

export default function roomReducer(state = initialState.room, action = {}) {
  switch (action.type) {
    case types.ROOM_SET_NAME:
      return Object.assign({}, state, {
        name: action.name
      });
    case types.ROOM_INPUT_CHANGE:
      return Object.assign({}, state, {
        inputValue: action.inputValue
      });
    case types.ROOM_INPUT_SEND:
      return Object.assign({}, state, {
        inputValue: ''
      });
    default:
      return state;
  }
}
