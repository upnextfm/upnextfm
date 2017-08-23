import * as types from 'actions/actionTypes';

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
 * @returns {{type: string, settings: *}}
 */
export function settingsUser(settings) {
  return {
    type: types.SETTINGS_USER,
    settings
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
