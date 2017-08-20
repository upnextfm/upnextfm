export default {
  nav: {
    isDrawerOpen: false
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
  room: {
    name:             '',
    inputValue:       '',
    isUsersCollapsed: false,
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
    codename:   'MD8flUkymrM',
    provider:   'youtube',
    subscribed: false
  }
};
