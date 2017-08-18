import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import { usersIndexOfUsername } from 'utils/users';

/**
 * Adds a user to the room users list
 *
 * @param {*} state
 * @param {{type: string, user: *}} action
 * @returns {*}
 */
function join(state, action) {
  if (usersIndexOfUsername(state.users, action.user.username) === -1) {
    const newState = Object.assign({}, state);
    newState.users.push(action.user);
    return newState;
  }
  return state;
}

/**
 * Removes a user from the room users list
 *
 * @param {*} state
 * @param {{type: string, username: string}} action
 * @returns {*}
 */
function leave(state, action) {
  const index = usersIndexOfUsername(state.repo, action.username);
  if (index !== -1) {
    const newState = Object.assign({}, state);
    newState.users.splice(index, 1);
    return newState;
  }
  return state;
}

/**
 * Sets all of the users in the room users list
 *
 * @param {*} state
 * @param {{type: string, users: Array}} action
 * @returns {*}
 */
function users(state, action) {
  const newUsers = [];
  action.users.forEach((user) => {
    newUsers.push(user.username);
  });
  return Object.assign({}, state, {
    users: newUsers
  });
}

/**
 *
 * @param {*} state
 * @param {{type: string, payload: *}} action
 * @returns {*}
 */
function payload(state, action) {
  switch (action.payload.cmd) {
    case types.CMD_SEND:
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
}

/**
 * Rooms reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function roomReducer(state = initialState.room, action = {}) {
  switch (action.type) {
    case types.ROOM_NAME:
      return Object.assign({}, state, {
        name: action.name
      });
    case types.ROOM_INPUT_CHANGE:
      return Object.assign({}, state, {
        inputValue: action.inputValue
      });
    case types.ROOM_SEND:
      return Object.assign({}, state, {
        inputValue: ''
      });
    case types.ROOM_JOIN:
      return join(state, action);
    case types.ROOM_LEAVE:
      return leave(state, action);
    case types.ROOM_USERS:
      return users(state, action);
    case types.ROOM_PAYLOAD:
      return payload(state, action);
    default:
      return state;
  }
}
