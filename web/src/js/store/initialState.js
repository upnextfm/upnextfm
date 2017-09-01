import Storage from 'api/Storage';

export default {
  layout: {
    isNavDrawerOpen:      false,
    isWindowFocused:      true,
    isLoginDialogOpen:    false,
    isRegisterDialogOpen: false,
    isHelpDialogOpen:     false,
    isUsersCollapsed:     Storage.getItem('layout:isUsersCollapsed', false),
    activeChat:           'room',
    errorMessage:         '',
    errorDuration:        30000,
    colsChatSide:         7,
    colsVideoSide:        5
  },
  user: {
    username:        '',
    roles:           [],
    isAuthenticated: false,
    isSubmitting:    false,
    error:           null
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
    socket: {
      pingInterval: 30000,
      uri:          ''
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
    isSending:     false,
    conversations: {}
  },
  player: {
    time:     0,
    duration: 0,
    status:   -1,    // -1 unstarted, 0 ended, 1 playing, 2 paused, 3 buffering, 4 cued
    isMuted:  false
  },
  playlist: {
    current: {},
    videos:  []
  },
  search: {
    results:      [],
    term:         '',
    isSubmitting: false,
    error:        null
  },
  users: {
    repo: []
  }
};
