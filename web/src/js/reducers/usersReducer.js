import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

function indexOfUsername(users, username) {
  for (let i = 0; i < users.length; i++) {
    if (users[i].username === username) {
      return i;
    }
  }

  return -1;
}

export default function usersReducer(state = initialState.users, action = {}) {
  switch (action.type) {
    case types.USERS_REPO_ADD:
      if (indexOfUsername(state.repo, action.user.username) === -1) {
        const newState = Object.assign({}, state);
        newState.repo.push(action.user);
        return newState;
      }
      return state;
    case types.USERS_REPO_REMOVE:
      const index = indexOfUsername(state.repo, action.username);
      if (index !== -1) {
        const newState = Object.assign({}, state);
        newState.repo.splice(index, 1);
        return newState;
      }
      return state;
    default:
      return state;
  }
}
