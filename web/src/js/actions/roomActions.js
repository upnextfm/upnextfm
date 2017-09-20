import * as types from './actionTypes';
import { usersRepoAdd, usersRepoAddMulti, usersRepoRemove } from 'actions/usersActions';
import { userRoles } from 'actions/userActions';
import { layoutToggleLoginDialog } from 'actions/layoutActions';
import { playlistSubscribe } from 'actions/playlistActions';
import { pmsSend } from 'actions/pmsActions';
import { settingsAll } from 'actions/settingsActions';

let noticeID     = 0;
let pingInterval = null;

function nextNoticeID() {
  noticeID += 1;
  return noticeID;
}

/**
 * @param {Function} dispatch
 * @param {*} payload
 * @param {Function} getState
 * @returns {*}
 */
function dispatchSocketPayload(dispatch, getState, payload) {
  switch (payload.cmd) {
    case types.CMD_JOINED:
      dispatch(usersRepoAdd(payload.user));
      dispatch({
        type: types.ROOM_JOINED,
        user: payload.user
      });
      if (getState().user.username !== payload.user.username) {
        dispatch(roomMessage({
          type:    'notice',
          id:      nextNoticeID(),
          date:    new Date(),
          message: `${payload.user.username} joined the room.`
        }));
      }
      break;
    case types.CMD_PARTED:
      dispatch({
        type:     types.ROOM_PARTED,
        username: payload.username
      });
      dispatch(roomMessage({
        type:    'notice',
        id:      nextNoticeID(),
        date:    new Date(),
        message: `${payload.username} left the room.`
      }));
      break;
    case types.CMD_ROLES:
      dispatch(userRoles(payload.roles));
      break;
    case types.CMD_REPO_USERS:
      dispatch(usersRepoAddMulti(payload.users));
      break;
    case types.CMD_USERS:
      dispatch(roomUsers(payload.users));
      break;
    case types.CMD_MESSAGES:
      dispatch(roomMessages(payload.messages));
      break;
    case types.CMD_SETTINGS:
      dispatch(settingsAll(payload.settings));
      break;
    case types.CMD_SEND:
      dispatch(roomMessage(payload.message));
      if (!getState().layout.isWindowFocused) {
        dispatch(roomIncrNumNewMessages());
      }
      break;
    case types.CMD_ME:
      dispatch(roomMessage(payload.message));
      if (!getState().layout.isWindowFocused) {
        dispatch(roomIncrNumNewMessages());
      }
      break;
    default:
      console.error('Unknown cmd', payload.cmd);
      break;
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
 * @param {string} msg
 * @param {Function} dispatch
 * @param {Function} getState
 * @param {*} api
 */
function handleCommandMe(msg, dispatch, getState, api) {
  const room = getState().room;
  if (room.name !== '') {
    if (!getState().user.isAuthenticated) {
      dispatch(layoutToggleLoginDialog());
    } else {
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        cmd:     types.CMD_ME,
        date:    (new Date()).toString(),
        message: msg
      });
    }
  }
}

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
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        cmd:       types.CMD_SEND,
        date:      (new Date()).toString(),
        textColor: getState().settings.user.textColor,
        message:   `[${textColor}]${msg}[/#]`
      });
    }
  }
}

const commands = {
  '/send': handleCommandSend,
  '/pm':   handleCommandPM,
  '/me':   handleCommandMe
};

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
 *
 * @param {Array} message
 * @returns {{type: *, message: *}}
 */
export function roomMessage(message) {
  return {
    type: types.ROOM_MESSAGE,
    message
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
 * @returns {{type: string}}
 */
export function roomResetNumNewMessages() {
  return {
    type: types.ROOM_RESET_NUM_NEW_MESSAGES
  };
}

/**
 * @returns {{type: string}}
 */
export function roomIncrNumNewMessages() {
  return {
    type: types.ROOM_INCR_NUM_NEW_MESSAGES
  };
}

/**
 *
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
    pingInterval = setInterval(() => {
      api.socket.publish(`${types.CHAN_ROOM}/${name}`, 'ping');
    }, getState().settings.socket.pingInterval);

    api.socket.subscribe(`${types.CHAN_ROOM}/${name}`, (uri, payload) => {
      if (payload === 'pong') {
        return;
      }
      dispatchSocketPayload(dispatch, getState, payload);
    });
  };
}
