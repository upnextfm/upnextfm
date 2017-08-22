import * as types from 'actions/actionTypes';
import { roomResetNumNewMessages } from 'actions/roomActions';

/**
 * @returns {{type: string}}
 */
export function layoutToggleNavDrawer() {
  return {
    type: types.LAYOUT_TOGGLE_NAV_DRAWER
  };
}

/**
 * @param {number} chatSide
 * @param {number} videoSide
 * @returns {{type: string, chatSide: number, videoSide: number}}
 */
export function layoutCols(chatSide, videoSide) {
  return {
    type: types.LAYOUT_COLS,
    chatSide,
    videoSide
  };
}

/**
 *
 * @param {string} status
 * @returns {Function}
 */
export function layoutWindowFocused(status) {
  return (dispatch) => {
    dispatch({
      type: types.LAYOUT_WINDOW_FOCUS,
      status
    });
    if (status === 'focus') {
      dispatch(roomResetNumNewMessages());
    }
  };
}
