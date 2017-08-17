import * as types from 'actions/actionTypes';
import { usersRepoAdd, usersRepoRemove } from 'actions/usersActions';

export function roomSend() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      dispatch({
        type: types.ROOM_SEND
      });
      api.socket.publish(`${types.CHAN_ROOM}/${room.name}`, {
        cmd:  types.CMD_SEND,
        date: (new Date()).toString(),
        msg:  room.inputValue
      });
    }
  };
}

export function roomUsers(users) {
  return {
    type: types.ROOM_USERS,
    users
  };
}

export function roomPayload(payload, uri) {
  return {
    type: types.ROOM_PAYLOAD,
    payload,
    uri
  };
}

export function roomLeave() {
  return (dispatch, getState, api) => {
    const room = getState().room;
    if (room.name !== '') {
      dispatch({
        type: types.ROOM_LEAVE
      });
      api.socket.unsubscribe(`${types.CHAN_ROOM}/${room.name}`);
    }
  };
}

export function roomJoin(name) {
  return (dispatch, getState, api) => {
    dispatch(roomLeave());
    dispatch({
      type: types.ROOM_JOIN,
      name
    });

    api.socket.subscribe(`${types.CHAN_ROOM}/${name}`, (uri, payload) => {
      switch (payload.cmd) {
        case types.CMD_JOIN:
          dispatch(usersRepoAdd(payload.user));
          break;
        case types.CMD_LEAVE:
          dispatch(usersRepoRemove(payload.username));
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

export function roomInputChange(inputValue) {
  return {
    type: types.ROOM_INPUT_CHANGE,
    inputValue
  };
}
