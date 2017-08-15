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
    messages: [
      {
        id:      1,
        message: 'Well, you just kind of copied and pasted from the other components, so ...',
        user:    {
          username: 'headzoo',
          avatar:   'https://headzoo.r.worldssl.net/images/me.jpg',
          profile:  '/u/headzoo',
          role:     'user'
        }
      },
      {
        id:      2,
        message: 'alright, i\'m at the cobble together stage',
        user:    {
          username: 'az4521',
          avatar:   'https://api.adorable.io/avatars/285/az4521%40headzoo.io',
          profile:  '/u/az4521',
          role:     'user'
        }
      },
      {
        id:      3,
        message: 'It\'s all good',
        user:    {
          username: 'headzoo',
          avatar:   'https://headzoo.r.worldssl.net/images/me.jpg',
          profile:  '/u/headzoo',
          role:     'user'
        }
      },
      {
        id:      4,
        message: 'should i cram buttons into the containers or try and make seperate classes for them?',
        user:    {
          username: 'az4521',
          avatar:   'https://api.adorable.io/avatars/285/az4521%40headzoo.io',
          profile:  '/u/az4521',
          role:     'user'
        }
      }
    ]
  },
  video: {

  },
  playlist: {

  },
  nav: {
    isDrawerOpen: false
  }
};
