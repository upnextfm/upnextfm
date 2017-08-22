import Storage from 'api/Storage';

export default {
  layout: {
    isNavDrawerOpen: false,
    isWindowFocused: true,
    colsChatSide:    7,
    colsVideoSide:   5
  },
  auth: {
    username:          '',
    isLoginDialogOpen: false,
    isAuthenticated:   false,
    isSubmitting:      false,
    error:             null
  },
  register: {
    isRegisterDialogOpen: false,
    isRegistered:         false,
    isSubmitting:         false,
    error:                null
  },
  settings: {
    showNotices: true
  },
  room: {
    name:             '',
    inputValue:       '',
    isUsersCollapsed: Storage.getItem('room:isUsersCollapsed', false),
    numNewMessages:   0,
    users:            [],
    messages:         []
  },
  users: {
    repo: []
  },
  video: {
    time:     0,
    duration: 0,
    status:   -1,    // -1 unstarted, 0 ended, 1 playing, 2 paused, 3 buffering, 4 cued
    isMuted:  false
  },
  playlist: {
    current: {}
  }
};
