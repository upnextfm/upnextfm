import * as types from 'actions/actionTypes';

/**
 * @returns {{type: string}}
 */
export function navToggleDrawer() {
  return {
    type: types.NAV_TOGGLE_DRAWER
  };
}
