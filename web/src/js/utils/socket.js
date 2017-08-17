import Auth from 'api/Auth';

export const CHAN_ROOM = 'app/room';
export const CHAN_AUTH = 'app/auth';
export const CMD_AUTH  = 'auth';
export const CMD_SEND  = 'send';
export const CMD_SENT  = 'sent';

let session = null;
const webSocket = WS.connect(_WS_URI); // eslint-disable-line no-undef
webSocket.on('socket/connect', (s) => {
  console.info('Connected');
  session = s;
});

webSocket.on('socket/disconnect', (error) => {
  console.info(`Disconnected for ${error.reason} with code ${error.code}`);
});

export default webSocket;

/**
 *
 * @param {string} chan
 * @param {*} payload
 * @returns {Promise}
 */
export function publish(chan, payload) {
  payload.token = Auth.getToken();
  session.publish(chan, payload);
  return new Promise((resolve) => {
    resolve();
  });
}

/**
 *
 * @param {string} chan
 * @param {Function} cb
 * @returns {Promise}
 */
export function subscribe(chan, cb) {
  session.subscribe(chan, cb);
  return new Promise((resolve) => {
    resolve();
  });
}

/**
 *
 * @param {string} chan
 * @returns {Promise}
 */
export function unsubscribe(chan) {
  session.unsubscribe(chan);
  return new Promise((resolve) => {
    resolve();
  });
}

