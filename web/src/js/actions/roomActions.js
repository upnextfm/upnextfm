import { dispatchPayload } from 'actions/dispatch';
import { settingsRoom, settingsUser } from 'actions/settingsActions';
import { layoutToggleLoginDialog } from 'actions/layoutActions';
import { playlistSubscribe } from 'actions/playlistActions';
import { pmsSend } from 'actions/pmsActions';
import * as types from './actionTypes';

let pingInterval = null;
const commands = {
  '/send': handleCommandSend,
  '/pm':   handleCommandPM,
  '/me':   handleCommandMe
};

/**
 * Handles the /send command
 *
 * @param {string} msg
 * @param {Function} dispatch
 * @param {Function} getState
 * @param {*} api
 */
function handleCommandSend(msg, dispatch, getState, api) {
  const room = getState().room;
  if (room.name !== '') {
    if (!getState().user.isAuthenticated) {
      dispatch(layoutToggleLoginDialog());
    } else {
      const textColor = getState().settings.user.textColor;
      const message   = `[${textColor}]${msg}[/#]`;
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        dispatch: [
          { action: 'send', args: [message] }
        ]
      });
    }
  }
}

/**
 * Handles the /pm command
 *
 * @param {string} msg
 * @param {Function} dispatch
 */
function handleCommandPM(msg, dispatch) {
  const [toUsername, ...messageParts] = msg.split(' ');
  const message = messageParts.join(' ').trim();
  if (toUsername !== '' && message !== '') {
    dispatch(pmsSend(toUsername, message));
  }
}

/**
 * Handles the /me command
 *
 * @param {string} message
 * @param {Function} dispatch
 * @param {Function} getState
 * @param {*} api
 */
function handleCommandMe(message, dispatch, getState, api) {
  const room = getState().room;
  if (room.name !== '') {
    if (!getState().user.isAuthenticated) {
      dispatch(layoutToggleLoginDialog());
    } else {
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        dispatch: [
          { action: 'me', args: [message] }
        ]
      });
    }
  }
}

/**
 * @param {string} name
 * @returns {Function}
 */
export function roomJoin(name) {
  return (dispatch, getState, api) => {
    dispatch(roomLeave());
    dispatch({
      type: types.ROOM_NAME,
      name
    });
    dispatch(playlistSubscribe());

    const interval = getState().settings.socket.pingInterval;
    const pingHandler = () => {
      dispatch(ping());
      api.socket.publish(`${types.CHAN_ROOM}/${name}`, 'ping');
    };
    pingHandler();
    pingInterval = setInterval(pingHandler, interval);

    api.socket.subscribe(`${types.CHAN_ROOM}/${name}`, (uri, payload) => {
      if (payload.dispatch !== undefined) {
        dispatchPayload(dispatch, payload);
      } else {
        console.error('Invalid payload');
      }
    });
  };
}

/**
 * @param {*} settings
 * @param {string} type
 * @returns {Function}
 */
export function roomSaveSettings(settings, type) {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      if (!getState().user.isAuthenticated) {
        return dispatch(layoutToggleLoginDialog());
      }

      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        dispatch: [
          { action: 'saveSettings', args: [settings, type] }
        ]
      });

      switch (type) {
        case 'user':
          return dispatch(settingsUser(settings));
        case 'room':
          return dispatch(settingsRoom(settings));
        default:
          console.log(`Invalid settings type "${type}".`);
          break;
      }
    }

    return true;
  };
}

/**
 * @param {string} inputValue
 * @returns {Function}
 */
export function roomSend(inputValue) {
  return (dispatch, getState, api) => {
    let value = inputValue.trim();
    if (value[0] !== '/') {
      const activeChat = getState().layout.activeChat;
      if (activeChat !== 'room') {
        value = `/pm ${activeChat} ${value}`;
      } else {
        value = `/send ${value}`;
      }
    }

    const idx = value.indexOf(' ');
    const cmd = (idx === -1) ? value : value.substr(0, idx).toLowerCase();
    const msg = (idx === -1) ? null : value.substr(idx + 1).trim();
    if (commands[cmd] !== undefined) {
      commands[cmd](msg, dispatch, getState, api);
    } else {
      console.info('Unknown command.');
    }

    dispatch({
      type: types.ROOM_SEND
    });
  };
}

/**
 *
 * @param {Array} users
 * @returns {{type: string, users: *}}
 */
export function roomUsers(users) {
  return {
    type: types.ROOM_USERS,
    users
  };
}

/**
 *
 * @param {Array} messages
 * @returns {{type: *, messages: *}}
 */
export function roomMessages(messages) {
  return {
    type: types.ROOM_MESSAGES,
    messages
  };
}

/**
 * @param {*} message
 * @returns {Function}
 */
export function roomMessage(message) {
  return (dispatch, getState) => {
    dispatch({
      type: types.ROOM_MESSAGE,
      message
    });
    if (!getState().layout.isWindowFocused) {
      dispatch(roomIncrNumNewMessages());
    }
  };
}

/**
 *
 * @param {Array} message
 * @returns {{type: *, message: *}}
 */
export function roomMe(message) {
  return {
    type: types.ROOM_ME,
    message
  };
}

/**
 *
 * @returns {Function}
 */
export function roomLeave() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      dispatch({
        type: types.ROOM_NAME,
        name: ''
      });
      api.socket.unsubscribe(`${types.CHAN_ROOM}/${room.name}`)
        .catch((err) => {
          console.info(err);
        });
      if (pingInterval) {
        clearInterval(pingInterval);
      }
    }
  };
}

/**
 * @param {*} user
 * @returns {{type: ROOM_JOINED, user: *}}
 */
export function roomJoined(user) {
  return {
    type: types.ROOM_JOINED,
    user
  };
}

/**
 * @param {string} username
 * @returns {{type: ROOM_PARTED, username: *}}
 */
export function roomParted(username) {
  return {
    type: types.ROOM_PARTED,
    username
  };
}

/**
 * @returns {{type: ROOM_RESET_NUM_NEW_MESSAGES}}
 */
export function roomResetNumNewMessages() {
  return {
    type: types.ROOM_RESET_NUM_NEW_MESSAGES
  };
}

/**
 * @returns {{type: ROOM_INCR_NUM_NEW_MESSAGES}}
 */
export function roomIncrNumNewMessages() {
  return {
    type: types.ROOM_INCR_NUM_NEW_MESSAGES
  };
}

/**
 * @returns {{type: ROOM_PING}}
 */
export function ping() {
  return {
    type: types.ROOM_PING
  };
}

/**
 * @param {number} time
 * @returns {{type: ROOM_PONG, time: *}}
 */
export function pong(time) {
  return {
    type: types.ROOM_PONG,
    time
  };
}
