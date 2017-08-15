console.info('App!');

const webSocket = WS.connect(_WS_URI);
webSocket.on('socket/connect', (session) => {
  console.info('Successfully Connected!');

  session.subscribe('app/room/lobby', (uri, payload) => {
    console.info('Received message', payload.msg);
  });
  session.publish('app/room/lobby', 'This is a message!');
});

webSocket.on('socket/disconnect', (error) => {
  // error provides us with some insight into the disconnection: error.reason and error.code
  console.info(`Disconnected for ${error.reason} with code ${error.code}`);
});
