import * as types from 'actions/actionTypes';

export function settingsAll(settings) {
  return {
    type: types.SETTINGS_ALL,
    settings
  };
}
