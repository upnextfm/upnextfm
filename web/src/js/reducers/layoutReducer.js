import * as types from 'actions/actionTypes';
import initialState from 'store/initialState';

/**
 * Layout reducer
 *
 * state = {
 *    isNavDrawerOpen:      false,
 *    isWindowFocused:      true,
 *    isLoginDialogOpen:    false,
 *    isRegisterDialogOpen: false,
 *    isHelpDialogOpen:     false,
 *    isUsersCollapsed:     false,
 *    activeChat:           'room',
 *    errorMessage:         '',
 *    colsChatSide:         7,
 *    colsVideoSide:        5
 * }
 *
 * @param {*} state
 * @param {*} action
 * @returns {*}
 */
export default function layoutReducer(state = initialState.layout, action = {}) {
  switch (action.type) {
    case types.LAYOUT_ERROR_MESSAGE:
      return Object.assign({}, state, {
        errorMessage: action.errorMessage
      });
    case types.LAYOUT_SWITCH_ACTIVE_CHAT:
      return Object.assign({}, state, {
        activeChat: action.activeChat.toLowerCase()
      });
    case types.LAYOUT_WINDOW_FOCUS:
      return Object.assign({}, state, {
        isWindowFocused: action.status === 'focus'
      });
    case types.LAYOUT_TOGGLE_NAV_DRAWER:
      return Object.assign({}, state, {
        isNavDrawerOpen: !state.isNavDrawerOpen
      });
    case types.LAYOUT_TOGGLE_LOGIN_DIALOG:
      return Object.assign({}, state, {
        isLoginDialogOpen: !state.isLoginDialogOpen
      });
    case types.LAYOUT_TOGGLE_REGISTER_DIALOG:
      return Object.assign({}, state, {
        isRegisterDialogOpen: !state.isRegisterDialogOpen
      });
    case types.LAYOUT_TOGGLE_HELP_DIALOG:
      return Object.assign({}, state, {
        isHelpDialogOpen: !state.isHelpDialogOpen
      });
    case types.LAYOUT_COLS:
      return Object.assign({}, state, {
        colsChatSide:  action.chatSide,
        colsVideoSide: action.videoSide
      });
    case types.LAYOUT_TOGGLE_USERS_COLLAPSED:
      return Object.assign({}, state, {
        isUsersCollapsed: !state.isUsersCollapsed
      });
    default:
      return state;
  }
}
