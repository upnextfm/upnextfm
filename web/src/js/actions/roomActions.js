import * as types from 'actions/actionTypes';
import { usersRepoAdd, usersRepoRemove } from 'actions/usersActions';

/**
 *
 * @returns {Function}
 */
export function roomSend() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        cmd:  types.CMD_SEND,
        date: (new Date()).toString(),
        msg:  room.inputValue
      });
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
 * @param {*} payload
 * @param {string} uri
 * @returns {{type: string, payload: *, uri: *}}
 */
export function roomPayload(payload, uri) {
  return {
    type: types.ROOM_PAYLOAD,
    payload,
    uri
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

    api.socket.subscribe(`${types.CHAN_ROOM}/${name}`, (uri, payload) => {
      console.info(payload);
      switch (payload.cmd) {
        case types.CMD_JOINED:
          dispatch(usersRepoAdd(payload.user));
          dispatch({
            type: types.ROOM_JOINED,
            user: payload.user
          });
          break;
        case types.CMD_PARTED:
          dispatch(usersRepoRemove(payload.username));
          dispatch({
            type:     types.ROOM_PARTED,
            username: payload.username
          });
          break;
        case types.CMD_USERS:
          dispatch(roomUsers(payload.users));
          break;
        default:
          dispatch(roomPayload(payload, uri));
          break;
      }
    });
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
