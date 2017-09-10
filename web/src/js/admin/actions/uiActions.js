import * as types from './types';

/**
 * @param {boolean} isLoading
 * @returns {{type: UI_LOADING, isLoading: boolean}}
 */
export function uiLoading(isLoading) {
  return {
    type: types.UI_LOADING,
    isLoading
  };
}
