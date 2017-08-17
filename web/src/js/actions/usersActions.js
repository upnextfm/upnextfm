import * as types from 'actions/actionTypes';

export function usersAdd(user) {
  return {
    type: types.USERS_ADD,
    user
  };
}

export function usersRemove(username) {
  return {
    type: types.USERS_REMOVE,
    username
  };
}
