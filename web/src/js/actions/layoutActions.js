import * as types from 'actions/actionTypes';
import { roomResetNumNewMessages } from 'actions/roomActions';
import { pmsNumNewMessages, pmsLoadConversation } from 'actions/pmsActions';

const loadedConversations = [];

/**
 * @param {string} errorMessage
 * @param {number} errorDuration
 * @returns {{type: string, errorMessage: *}}
 */
export function layoutErrorMessage(errorMessage, errorDuration = 30000) {
  return {
    type: types.LAYOUT_ERROR_MESSAGE,
    errorMessage,
    errorDuration
  };
}

/**
 *
 * @param {string} activeChat
 * @returns {Function}
 */
export function layoutSwitchActiveChat(activeChat) {
  return (dispatch) => {
    const ac = activeChat.toLowerCase();
    if (ac !== 'room') {
      if (loadedConversations.indexOf(ac) === -1) {
        dispatch(pmsLoadConversation(ac));
        loadedConversations.push(ac);
      }

      dispatch(pmsNumNewMessages(ac, 0));
      dispatch({
        type:       types.LAYOUT_SWITCH_ACTIVE_CHAT,
        activeChat: ac
      });
    } else {
      dispatch({
        type:       types.LAYOUT_SWITCH_ACTIVE_CHAT,
        activeChat: ac
      });
    }
  };
}

/**
 * @returns {{type: string}}
 */
export function layoutToggleNavDrawer() {
  return {
    type: types.LAYOUT_TOGGLE_NAV_DRAWER
  };
}

/**
 *
 * @returns {Function}
 */
export function layoutToggleUsersCollapsed() {
  return (dispatch, getState, api) => {
    dispatch({
      type: types.LAYOUT_TOGGLE_USERS_COLLAPSED
    });
    api.storage.setItem('layout:isUsersCollapsed', getState().layout.isUsersCollapsed);
  };
}

/**
 * @returns {{type: string}}
 */
export function layoutToggleRegisterDialog() {
  return {
    type: types.LAYOUT_TOGGLE_REGISTER_DIALOG
  };
}

/**
 * @returns {{type: string}}
 */
export function layoutToggleLoginDialog() {
  return {
    type: types.LAYOUT_TOGGLE_LOGIN_DIALOG
  };
}

/**
 * @returns {{type: string}}
 */
export function layoutToggleHelpDialog() {
  return {
    type: types.LAYOUT_TOGGLE_HELP_DIALOG
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
