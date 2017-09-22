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
      return {
        ...state,
        errorMessage:  action.errorMessage,
        errorDuration: action.errorDuration
      };
    case types.LAYOUT_SWITCH_ACTIVE_CHAT:
      return {
        ...state,
        activeChat: action.activeChat.toLowerCase()
      };
    case types.LAYOUT_WINDOW_FOCUS:
      return {
        ...state,
        isWindowFocused: action.status === 'focus'
      };
    case types.LAYOUT_TOGGLE_NAV_DRAWER:
      return {
        ...state,
        isNavDrawerOpen: !state.isNavDrawerOpen
      };
    case types.LAYOUT_TOGGLE_LOGIN_DIALOG:
      return {
        ...state,
        isLoginDialogOpen: !state.isLoginDialogOpen
      };
    case types.LAYOUT_TOGGLE_REGISTER_DIALOG:
      return {
        ...state,
        isRegisterDialogOpen: !state.isRegisterDialogOpen
      };
    case types.LAYOUT_TOGGLE_HELP_DIALOG:
      return {
        ...state,
        isHelpDialogOpen: !state.isHelpDialogOpen
      };
    case types.LAYOUT_TOGGLE_ROOM_SETTINGS_DIALOG:
      return {
        ...state,
        isRoomSettingsDialogOpen: !state.isRoomSettingsDialogOpen
      };
    case types.LAYOUT_COLS:
      return {
        ...state,
        colsChatSide:  action.chatSide,
        colsVideoSide: action.videoSide
      };
    case types.LAYOUT_TOGGLE_USERS_COLLAPSED:
      return {
        ...state,
        isUsersCollapsed: !state.isUsersCollapsed
      };
    default:
      return state;
  }
}
