import * as types from 'actions/actionTypes';

export function usersRepoAdd(user) {
  return {
    type: types.USERS_REPO_ADD,
    user
  };
}

export function usersRepoAddMulti(users) {
  return {
    type: types.USERS_REPO_ADD_MULTI,
    users
  };
}

export function usersRepoRemove(username) {
  return {
    type: types.USERS_REPO_REMOVE,
    username
  };
}
