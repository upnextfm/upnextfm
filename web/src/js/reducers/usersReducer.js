import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

export default function usersReducer(state = initialState.users, action = {}) {
  switch (action.type) {
    default:
      return state;
  }
}
