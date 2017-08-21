import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Nav reducer
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function layoutReducer(state = initialState.layout, action = {}) {
  switch (action.type) {
    case types.LAYOUT_TOGGLE_NAV_DRAWER:
      return Object.assign({}, state, {
        isNavDrawerOpen: !state.isNavDrawerOpen
      });
    case types.LAYOUT_COLS:
      return Object.assign({}, state, {
        colsChatSide:  action.chatSide,
        colsVideoSide: action.videoSide
      });
    default:
      return state;
  }
}
