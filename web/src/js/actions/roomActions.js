import * as types from 'actions/actionTypes';

export function roomSetName(name) {
  return {
    type: types.ROOM_SET_NAME,
    name
  };
}

export function roomInputChange(inputValue) {
  return {
    type: types.ROOM_INPUT_CHANGE,
    inputValue
  };
}

export function roomInputSend() {
  return {
    type: types.ROOM_INPUT_SEND
  };
}
