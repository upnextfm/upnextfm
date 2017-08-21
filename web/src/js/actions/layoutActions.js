import * as types from 'actions/actionTypes';

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
