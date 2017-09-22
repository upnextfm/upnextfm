import * as types from 'actions/actionTypes';
import { roomSaveSettings } from 'actions/roomActions';

/**
 *
 * @param {*} settings
 * @returns {{type: string, settings: *}}
 */
export function settingsAll(settings) {
  return {
    type: types.SETTINGS_ALL,
    settings
  };
}

/**
 *
 * @param {*} settings
 * @returns {Function}
 */
export function settingsUser(settings) {
  return (dispatch, getState) => {
    dispatch({
      type: types.SETTINGS_USER,
      settings
    });
    dispatch(roomSaveSettings(getState().settings.user, 'user'));
  };
}

/**
 *
 * @param {*} settings
 * @returns {{type: string, settings: *}}
 */
export function settingsSite(settings) {
  return {
    type: types.SETTINGS_SITE,
    settings
  };
}

/**
 *
 * @param {*} settings
 * @returns {{type: string, settings: *}}
 */
export function settingsRoom(settings) {
  return {
    type: types.SETTINGS_ROOM,
    settings
  };
}

/**
 *
 * @param {*} settings
 * @returns {{type: string, settings: *}}
 */
export function settingsSocket(settings) {
  return {
    type: types.SETTINGS_SOCKET,
    settings
  };
}
