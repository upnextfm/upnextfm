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
    case types.USERS_ADD:
      if (indexOfUsername(state, action.user.username) === -1) {
        state.push(action.user);
      }
      return state;
    case types.USERS_REMOVE:
      const index = indexOfUsername(state, action.username);
      if (index !== -1) {
        state.splice(index, 1);
      }
      return state;
    default:
      return state;
  }
}
