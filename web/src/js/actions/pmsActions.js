import { dispatchPayload } from 'actions/dispatch';
import { layoutSwitchActiveChat, layoutErrorMessage } from 'actions/layoutActions';
import * as types from 'actions/actionTypes';

/**
 * @returns {Function}
 */
export function pmsSubscribe() {
  return (dispatch, getState, api) => {
    if (!getState().user.isAuthenticated) {
      return;
    }

    dispatch({
      type: types.PMS_SUBSCRIBED
    });
    api.socket.subscribe(types.CHAN_PMS, (uri, payload) => {
      if (payload.dispatch !== undefined) {
        dispatchPayload(dispatch, payload);
      } else {
        console.error('Invalid payload');
      }
    });
  };
}

/**
 * @returns {{type: string, isSending: bool}}
 */
export function pmsSending(isSending) {
  return {
    type: types.PMS_SENDING,
    isSending
  };
}

/**
 * @param {string} username
 * @param {number} numNewMessages
 * @returns {{type: string, username: string, numNewMessages: number}}
 */
export function pmsNumNewMessages(username, numNewMessages) {
  return {
    type: types.PMS_NUM_NEW_MESSAGES,
    username,
    numNewMessages
  };
}

/**
 * @param {string} to
 * @param {array} conversation
 * @returns {{type: *, to: *, conversation: *}}
 */
export function pmsLoad(to, conversation) {
  return {
    type: types.PMS_LOAD,
    to,
    conversation
  };
}

/**
 * @param {string} username
 * @returns {Function}
 */
export function pmsLoadConversation(username) {
  return (dispatch, getState, api) => {
    if (!getState().user.isAuthenticated) {
      return;
    }
    api.socket.dispatch(types.CHAN_PMS, 'load', [
      username
    ]);
  };
}

/**
 * @param {*} message
 * @returns {{type: *, message: *}}
 */
export function pmsReceive(message) {
  return {
    type: types.PMS_RECEIVE,
    message
  };
}

/**
 * @param {*} message
 * @returns {Function}
 */
export function pmsSent(message) {
  return (dispatch, getState) => {
    if (getState().pms.isSending) {
      dispatch(layoutSwitchActiveChat(message.to));
    }
    return dispatch({
      type: types.PMS_SENT,
      message
    });
  };
}

/**
 *
 * @param {string} to
 * @param {string} message
 * @returns {Function}
 */
export function pmsSend(to, message) {
  return (dispatch, getState, api) => {
    const user = getState().user;
    if (!user.isAuthenticated) {
      return;
    }
    if (user.username.toLowerCase() === to.toLowerCase()) {
      dispatch(layoutErrorMessage('Cannot send a PM to yourself.'));
      return;
    }

    const textColor = getState().settings.user.textColor;
    dispatch(pmsSending(true));
    api.socket.dispatch(types.CHAN_PMS, 'send', [
      to,
      `[${textColor}]${message}[/#]`
    ]);
  };
}
