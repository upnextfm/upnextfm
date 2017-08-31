
class Socket {

  /**
   * Constructor
   */
  constructor() {
    this.listeners = [];
    this.session   = null;
    this.connected = false;
    this.debug     = true;
    this.uri       = null;
  }

  /**
   *
   * @param {string} uri
   */
  connect = (uri) => {
    this.info(`Connecting to ${uri}`);
    this.uri = uri;

    return new Promise((resolve, reject) => {
      try {
        ab.connect(uri,               // eslint-disable-line no-undef
          (session) => {
            this.info('Connected!');
            this.session   = session;
            this.connected = true;
            this.fire({
              type: 'socket/connect',
              data: session
            });
            resolve(this);
          },
          (code, reason) => {
            this.info(`Disconnected ${code}: ${reason}`);
            this.session   = null;
            this.connected = false;
            this.fire({
              type: 'socket/disconnect',
              data: {
                code,
                reason
              }
            });

            if (code === 3) {
              setTimeout(() => {
                this.connect(uri);
              }, 5000);
            }
          });
      } catch (error) {
        this.error(error);
        reject(new Error(error));
      }
    });
  };

  reconnect = () => {
    this.session.close();
    return this.connect(this.uri);
  };

  /**
   *
   * @param {string} topic
   * @param {Function} cb
   * @returns {Promise}
   */
  subscribe = (topic, cb) => {
    return new Promise((resolve) => {
      if (this.session) {
        this.session.subscribe(topic, cb);
      }
      resolve(this);
    });
  };

  /**
   *
   * @param {string} topic
   * @returns {Promise}
   */
  unsubscribe = (topic) => {
    return new Promise((resolve) => {
      if (this.session) {
        this.session.unsubscribe(topic);
      }
      resolve(this);
    });
  };

  /**
   *
   * @param {string} chan
   * @param {*} payload
   * @returns {Promise}
   */
  publish = (chan, payload) => {
    return new Promise((resolve) => {
      if (this.session) {
        this.session.publish(chan, payload);
      }
      resolve(this);
    });
  };

  /**
   *
   * @param {string} type
   * @param {Function} listener
   */
  on = (type, listener) => {
    if (this.listeners[type] === undefined) {
      this.listeners[type] = [];
    }
    this.listeners[type].push(listener);
  };

  /**
   *
   * @param {string} type
   * @param {Function} listener
   */
  off = (type, listener) => {
    if (Array.isArray(this.listeners[type])) {
      const index = this.listeners[type].indexOf(listener);
      if (index !== -1) {
        this.listeners[type].splice(index, 1);
      }
    }
  };

  /**
   *
   * @param {*} event
   */
  fire = (event) => {
    if (typeof event === 'string') {
      event = { type: event };
    }
    if (!event.target) {
      event.target = this;
    }
    if (!event.type) {
      throw new Error("Event object missing 'type' property.");
    }

    if (Array.isArray(this.listeners[event.type])) {
      const listeners = this.listeners[event.type];
      for (let i = 0, len = listeners.length; i < len; i++) {
        listeners[i].call(this, event.data);
      }
    }
  };

  /**
   *
   * @param {string} msg
   */
  info = (msg) => {
    if (this.debug) {
      console.info(`Socket: ${msg}`);
    }
  };

  /**
   *
   * @param {string} msg
   */
  error = (msg) => {
    if (this.debug) {
      console.error(`Socket: ${msg}`);
    }
  };
}

export default new Socket();
