import * as types from 'actions/actionTypes';
import { sanitizeMessage } from 'utils/messages';
import initialState from 'store/initialState';
import store from 'store/store';

/**
 * Loads the conversation with another user
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function load(state, action) {
  const msgs = [];
  for (let i = 0; i < action.conversation.length; i++) {
    msgs.push(sanitizeMessage(action.conversation[i]));
  }

  const key = action.to.toLowerCase();
  const conversations = Object.assign({}, state.conversations);
  if (conversations[key] === undefined) {
    conversations[key] = {
      messages:       msgs,
      numNewMessages: 0
    };
  } else {
    conversations[key].messages = msgs;
    conversations[key].numNewMessages = 0;
  }

  return {
    ...state,
    isSending: false,
    conversations
  };
}

/**
 * Adds a pm to the user's conversations
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function receive(state, action) {
  const msg           = sanitizeMessage(action.message);
  const key           = msg.from.toLowerCase();
  const activeChat    = store.getState().layout.activeChat;
  const conversations = Object.assign({}, state.conversations);

  if (conversations[key] === undefined) {
    conversations[key] = {
      messages:       [msg],
      numNewMessages: 1
    };
  } else {
    conversations[key].messages.push(msg);
    if (activeChat !== key) {
      conversations[key].numNewMessages += 1;
    }
  }

  return {
    ...state,
    isSending: false,
    conversations
  };
}

/**
 * Adds a pm this user sent to another user
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function sent(state, action) {
  const msg           = sanitizeMessage(action.message);
  const key           = msg.to.toLowerCase();
  const conversations = Object.assign({}, state.conversations);

  if (conversations[key] === undefined) {
    conversations[key] = {
      messages:       [msg],
      numNewMessages: 0
    };
  } else {
    conversations[key].messages.push(msg);
  }

  return {
    ...state,
    isSending: false,
    conversations
  };
}

/**
 * Sets the number of new messages from a user
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
function numNewMessages(state, action) {
  if (state.conversations[action.username] !== undefined) {
    const conversations = Object.assign({}, state.conversations);
    conversations[action.username].numNewMessages = action.numNewMessages;
    return {
      ...state,
      conversations
    };
  }

  return state;
}

/**
 * Private messages reducer
 *
 * state = {
 *    isSubscribed:  false,
 *    isSending:     false,
 *    conversations: {
 *       headzoo: { messages: [], numNewMessages: 0 },
 *       az4521:  { messages: [], numNewMessages: 0 }
 *   }
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function pmsReducer(state = initialState.pms, action = {}) {
  switch (action.type) {
    case types.PMS_SUBSCRIBED:
      return {
        ...state,
        isSubscribed: true,
        isSending:    false
      };
    case types.PMS_SENDING:
      return {
        ...state,
        isSending: true
      };
    case types.PMS_LOAD:
      return load(state, action);
    case types.PMS_SENT:
      return sent(state, action);
    case types.PMS_RECEIVE:
      return receive(state, action);
    case types.PMS_NUM_NEW_MESSAGES:
      return numNewMessages(state, action);
    default:
      return state;
  }
}
