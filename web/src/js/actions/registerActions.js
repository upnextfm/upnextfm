import * as types from 'actions/actionTypes';
import Auth from 'api/Auth';

/**
 * @returns {{type: string}}
 */
export function registerToggleDialog() {
  return {
    type: types.REGISTER_TOGGLE_DIALOG
  };
}

/**
 * @returns {{type: string}}
 */
export function registerReset() {
  return {
    type: types.REGISTER_RESET
  };
}
