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
  users: {
    repo: [
      {
        username: 'headzoo',
        avatar:   'https://api.adorable.io/avatars/50/sean%40upnext.fm',
        profile:  'https://upnext.fm/u/headzoo',
        roles:    ['user']
      },
      {
        username: 'az4521',
        avatar:   'https://api.adorable.io/avatars/50/az4521%40upnext.fm',
        profile:  'https://upnext.fm/u/az4521',
        roles:    ['user']
      }
    ]
  },
  room: {
    name:       '',
    inputValue: '',
    users:      ['headzoo', 'az4521'],
    messages:   [
      {
        id:      1,
        date:    new Date(),
        from:    'headzoo',
        message: 'Well, you just kind of copied and pasted from the other components, so ...'
      },
      {
        id:      2,
        date:    new Date(),
        from:    'az4521',
        message: 'alright, i\'m at the cobble together stage'
      },
      {
        id:      3,
        date:    new Date(),
        from:    'headzoo',
        message: 'It\'s all good'
      },
      {
        id:      4,
        date:    new Date(),
        from:    'az4521',
        message: 'i updated the layout of the room a bit, now the video always stretches to be 16:9, and the playlist box takes up the remaining space. also the buttons added to the button container now wrap around and make a second row.'
      }
    ]
  },
  video: {

  },
  playlist: {

  }
};
