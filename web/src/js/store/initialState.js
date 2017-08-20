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
    isMuted:  false
  },
  playlist: {
    codename:   'MD8flUkymrM',
    provider:   'youtube',
    subscribed: false
  }
};
