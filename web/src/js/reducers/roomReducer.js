import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import { sanitizeMessage } from 'utils/messages';

/**
 * Adds a user to the room users list
 *
 * @param {*} state
 * @param {{type: string, user: *}} action
 * @returns {*}
 */
function joined(state, action) {
  const username = action.user.username;
  const newState = Object.assign({}, state);
  if (newState.users.indexOf(username) === -1) {
    newState.users.push(username);
  }
  return newState;
}

/**
 * Removes a user from the room users list
 *
 * @param {*} state
 * @param {{type: string, username: string}} action
 * @returns {*}
 */
function parted(state, action) {
  const username = action.username;
  const newState = Object.assign({}, state);
  newState.users = newState.users.filter((un) => {
    return un !== username;
  });
  return newState;
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
  action.users.forEach((username) => {
    newUsers.push(username);
  });
  return Object.assign({}, state, {
    users: newUsers
  });
}

/**
 * Sets the recent room messages
 *
 * @param {*} state
 * @param {{type: string, messages: Array}} action
 * @returns {*}
 */
function messages(state, action) {
  const newMessages = [];
  action.messages.forEach((msg) => {
    newMessages.push(sanitizeMessage(msg));
  });

  return Object.assign({}, state, {
    messages: newMessages
  });
}

/**
 * Adds a message
 *
 * @param {*} state
 * @param {{type: string, message: *}} action
 * @returns {*}
 */
function message(state, action) {
  const msgs = state.messages.slice();
  msgs.push(sanitizeMessage(action.message));

  return Object.assign({}, state, {
    messages: msgs
  });
}

/**
 * Rooms reducer
 *
 * state = {
 *    name:           '',
 *    inputValue:     '',
 *    users:          [],
 *    messages:       [],
 *    numNewMessages: 0
 * }
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
    case types.ROOM_INCR_NUM_NEW_MESSAGES:
      return Object.assign({}, state, {
        numNewMessages: state.numNewMessages + 1
      });
    case types.ROOM_RESET_NUM_NEW_MESSAGES:
      return Object.assign({}, state, {
        numNewMessages: 0
      });
    case types.ROOM_JOINED:
      return joined(state, action);
    case types.ROOM_PARTED:
      return parted(state, action);
    case types.ROOM_USERS:
      return users(state, action);
    case types.ROOM_MESSAGES:
      return messages(state, action);
    case types.ROOM_MESSAGE:
      return message(state, action);
    default:
      return state;
  }
}
