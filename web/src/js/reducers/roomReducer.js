import * as types from 'actions/actionTypes';
import * as socket from 'utils/socket';
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
    case types.ROOM_USERS:
      const users = [];
      action.users.forEach((user) => {
        users.push(user.username);
      });
      return Object.assign({}, state, {
        users
      });
    case types.ROOM_PAYLOAD:
      switch (action.payload.cmd) {
        case socket.CMD_SEND:
          const messages = state.messages;
          const message  = action.payload.msg;
          message.date   = new Date(message.date);
          messages.push(message);
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
