import * as types from 'actions/actionTypes';
import { usersRepoAdd, usersRepoAddMulti, usersRepoRemove } from 'actions/usersActions';
import { authToggleLoginDialog } from 'actions/authActions';
import { playlistSubscribe } from 'actions/playlistActions';
import { settingsAll } from 'actions/settingsActions';

let noticeID = 0;

function nextNoticeID() {
  noticeID += 1;
  return noticeID;
}

/**
 *
 * @returns {Function}
 */
export function roomSend() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      if (!getState().auth.isAuthenticated) {
        dispatch(authToggleLoginDialog());
      } else {
        api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
          cmd:     types.CMD_SEND,
          date:    (new Date()).toString(),
          message: room.inputValue
        });
      }
      dispatch({
        type: types.ROOM_SEND
      });
    }
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
      api.socket.unsubscribe(`${types.CHAN_ROOM}/${room.name}`);
    }
  };
}

/**
 *
 * @param {string} inputValue
 * @returns {{type: string, inputValue: *}}
 */
export function roomInputChange(inputValue) {
  return {
    type: types.ROOM_INPUT_CHANGE,
    inputValue
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

    api.socket.subscribe(`${types.CHAN_ROOM}/${name}`, (uri, payload) => {
      switch (payload.cmd) {
        case types.CMD_JOINED:
          dispatch(usersRepoAdd(payload.user));
          dispatch({
            type: types.ROOM_JOINED,
            user: payload.user
          });
          if (getState().auth.username !== payload.user.username) {
            dispatch(roomMessage({
              type:    'notice',
              id:      nextNoticeID(),
              date:    new Date(),
              message: `${payload.user.username} joined the room.`
            }));
          }
          break;
        case types.CMD_PARTED:
          dispatch(usersRepoRemove(payload.username));
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
        default:
          console.error('Unknown cmd', payload.cmd);
          break;
      }
    });
  };
}
