import * as types from 'actions/actionTypes';
import * as socket from 'utils/socket';

export function roomSend() {
  return (dispatch, getState, { publish }) => {
    const room = getState().room;
    if (room.name !== '') {
      dispatch({
        type: types.ROOM_SEND
      });
      publish(`${socket.CHAN_ROOM}/${room.name}`, {
        cmd: socket.CMD_SEND,
        msg: room.inputValue
      });
    }
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
  return (dispatch, getState, { unsubscribe }) => {
    const room = getState().room;
    if (room.name !== '') {
      dispatch({
        type: types.ROOM_LEAVE
      });
      unsubscribe(`${socket.CHAN_ROOM}/${room.name}`);
    }
  };
}

export function roomJoin(name) {
  return (dispatch, getState, { subscribe }) => {
    dispatch(roomLeave());
    dispatch({
      type: types.ROOM_JOIN,
      name
    });
    subscribe(`${socket.CHAN_ROOM}/${name}`, (uri, payload) => {
      dispatch(roomPayload(payload, uri));
    });
  };
}

export function roomInputChange(inputValue) {
  return {
    type: types.ROOM_INPUT_CHANGE,
    inputValue
  };
}
