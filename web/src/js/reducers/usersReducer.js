import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';
import { usersIndexOfUsername } from 'utils/users';

/**
 * Adds a user to user repo
 *
 * @param {*} state
 * @param {{type: string, user: *}} action
 * @returns {*}
 */
function addRepoUser(state, action) {
  if (usersIndexOfUsername(state.repo, action.user.username) === -1) {
    const newState = Object.assign({}, state);
    newState.repo.push(action.user);
    return newState;
  }
  return state;
}

/**
 * Adds multiple to user repo
 *
 * @param {*} state
 * @param {{type: string, users: *}} action
 * @returns {*}
 */
function addRepoUsers(state, action) {
  return Object.assign({}, state, {
    repo: action.users
  });
}

/**
 * Removes a user from user repo
 *
 * @param {*} state
 * @param {{type: string, username: string}} action
 * @returns {*}
 */
function removeRepoUser(state, action) {
  const index = usersIndexOfUsername(state.repo, action.username);
  if (index !== -1) {
    const newState = Object.assign({}, state);
    newState.repo.splice(index, 1);
    return newState;
  }
  return state;
}

/**
 * User repo reducer
 *
 * state = {
 *    repo: []
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function usersReducer(state = initialState.users, action = {}) {
  switch (action.type) {
    case types.USERS_REPO_ADD:
      return addRepoUser(state, action);
    case types.USERS_REPO_ADD_MULTI:
      return addRepoUsers(state, action);
    case types.USERS_REPO_REMOVE:
      return removeRepoUser(state, action);
    default:
      return state;
  }
}
