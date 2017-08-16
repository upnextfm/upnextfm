import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

export default function roomReducer(state = initialState.room, action = {}) {
  switch (action.type) {
    case types.ROOM_JOIN:
      return Object.assign({}, state, {
        name: action.name
      });
    case types.ROOM_LEAVE:
      return Object.assign({}, state, {
        name: ''
      });
    case types.ROOM_INPUT_CHANGE:
      return Object.assign({}, state, {
        inputValue: action.inputValue
      });
    case types.ROOM_SEND:
      return Object.assign({}, state, {
        inputValue: ''
      });
    case types.ROOM_PAYLOAD:
      switch (action.payload.cmd) {
        case 'sent':
          const messages = state.messages;
          messages.push(action.payload.msg);
          return Object.assign({}, state, {
            messages
          });
          break;
        default:
          return state;
      }
      break;
    default:
      return state;
  }
}
