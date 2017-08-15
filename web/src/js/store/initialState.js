export default {
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
    name:       '',
    inputValue: '',
    users:      [
      {
        username: 'headzoo',
        avatar:   'https://headzoo.r.worldssl.net/images/me.jpg',
        profile:  '/u/headzoo',
        role:     'user'
      },
      {
        username: 'az4521',
        avatar:   'https://api.adorable.io/avatars/285/az4521%40headzoo.io',
        profile:  '/u/az4521',
        role:     'user'
      }
    ],
    messages:   []
  },
  nav: {
    isDrawerOpen: false
  }
};
