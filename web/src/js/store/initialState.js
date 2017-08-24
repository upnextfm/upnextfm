import Storage from 'api/Storage';

export default {
  layout: {
    isNavDrawerOpen:      false,
    isWindowFocused:      true,
    isLoginDialogOpen:    false,
    isRegisterDialogOpen: false,
    isUsersCollapsed:     Storage.getItem('layout:isUsersCollapsed', false),
    activeChat:           'room',
    colsChatSide:         7,
    colsVideoSide:        5
  },
  auth: {
    username:        '',
    error:           null,
    isAuthenticated: false,
    isSubmitting:    false
  },
  register: {
    error:        null,
    isRegistered: false,
    isSubmitting: false
  },
  settings: {
    user: {
      showNotices: true
    },
    site: {},
    room: {}
  },
  room: {
    name:           '',
    inputValue:     '',
    numNewMessages: 0,
    users:          [],
    messages:       []
  },
  pms: {
    isSubscribed:  false,
    conversations: []
  },
  users: {
    repo: []
  },
  player: {
    time:     0,
    duration: 0,
    status:   -1,    // -1 unstarted, 0 ended, 1 playing, 2 paused, 3 buffering, 4 cued
    isMuted:  false
  },
  playlist: {
    current: {}
  }
};
