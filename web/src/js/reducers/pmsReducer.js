import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import store from 'store/store';

/**
 * Adds a pm to the user's conversations
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function receive(state, action) {
  let found           = false;
  const activeChat    = store.getState().layout.activeChat;
  const conversations = state.conversations.slice();

  for (let i = 0; i < conversations.length; i++) {
    if (conversations[i].from === action.message.from) {
      conversations[i].messages.push(action.message);
      if (activeChat !== action.message.from) {
        conversations[i].numNewMessages += 1;
      }
      found = true;
      break;
    }
  }
  if (!found) {
    conversations.push({
      from:           action.message.from,
      messages:       [action.message],
      numNewMessages: 1
    });
  }

  return Object.assign({}, state, {
    isSending: false,
    conversations
  });
}

/**
 * Adds a pm this user sent to another user
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function sent(state, action) {
  let found           = false;
  const conversations = state.conversations.slice();
  for (let i = 0; i < conversations.length; i++) {
    if (conversations[i].from === action.message.to) {
      conversations[i].messages.push(action.message);
      found = true;
      break;
    }
  }
  if (!found) {
    conversations.push({
      from:           action.message.to,
      messages:       [action.message],
      numNewMessages: 0
    });
  }

  return Object.assign({}, state, {
    isSending: false,
    conversations
  });
}

/**
 * Private messages reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function pmsReducer(state = initialState.pms, action = {}) {
  switch (action.type) {
    case types.PMS_SUBSCRIBED:
      return Object.assign({}, state, {
        isSubscribed: true,
        isSending:    false
      });
    case types.PMS_SENT:
      return sent(state, action);
    case types.PMS_RECEIVE:
      return receive(state, action);
    case types.PMS_SENDING:
      return Object.assign({}, state, {
        isSending: true
      });
    default:
      return state;
  }
}
