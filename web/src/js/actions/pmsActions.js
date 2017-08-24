import * as types from 'actions/actionTypes';
import { layoutSwitchActiveChat } from 'actions/layoutActions';

/**
 * @param {Function} dispatch
 * @param {{cmd: string}} payload
 * @returns {*}
 */
function dispatchSocketPayload(dispatch, payload) {
  switch (payload.cmd) {
    case types.CMD_RECEIVE:
      return dispatch({
        type:    types.PMS_RECEIVE,
        message: payload.message
      });
    case types.CMD_SENT:
      return dispatch({
        type:    types.PMS_SENT,
        message: payload.message
      });
    default:
      return console.error(`Invalid comment ${payload.cmd}`);
  }
}

/**
 * @returns {Function}
 */
export function pmsSubscribe() {
  return (dispatch, getState, api) => {
    if (!api.auth.isAuthenticated()) {
      return;
    }

    dispatch({
      type: types.PMS_SUBSCRIBED
    });
    api.socket.subscribe(types.CHAN_PMS, (uri, payload) => {
      dispatchSocketPayload(dispatch, payload);
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
    if (!api.auth.isAuthenticated()) {
      return;
    }

    dispatch(layoutSwitchActiveChat(to));
    api.socket.publish(types.CHAN_PMS, {
      cmd:     types.CMD_SEND,
      message: {
        to,
        message
      }
    });
  };
}
