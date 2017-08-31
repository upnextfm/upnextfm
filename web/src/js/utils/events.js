export class Event {
  constructor(value = '') {
    this._value = value;
    this._type  = '';
    this._isPropagationStopped = false;
  }

  /**
   * @returns {string}
   */
  get type() {
    return this._type;
  }

  /**
   *
   * @returns {*}
   */
  get value() {
    return this._value;
  }

  /**
   *
   */
  stopPropagation = () => {
    this._isPropagationStopped = true;
  };

  /**
   *
   * @returns {boolean}
   */
  isPropagationStopped = () => {
    return this._isPropagationStopped;
  };
}

export class EventDispatcher {
  constructor() {
    this.listeners = {};
  }

  /**
   *
   * @param {string} event
   * @param {Function} listener
   * @returns {Function}
   */
  on = (event, listener) => {
    if (this.listeners[event] === undefined) {
      this.listeners[event] = [];
    }
    this.listeners[event].push(listener);

    return () => {
      this.off(event, listener);
    };
  };

  /**
   *
   * @param {string} event
   * @param {Function} listener
   */
  off = (event, listener) => {
    if (this.listeners[event] !== undefined) {
      const index = this.listeners[event].indexOf(listener);
      if (index !== -1) {
        this.listeners[event].splice(index, 1);
      }
    }
  };

  /**
   *
   * @param {string} event
   * @param {Event|*} args
   * @return {Event}
   */
  trigger = (event, args = null) => {
    if (!(args instanceof Event)) {
      args = new Event(args);
    }
    args._type = event;

    if (this.listeners[event] !== undefined) {
      const listeners = this.listeners[event];
      for (let i = 0; i < listeners.length; i++) {
        listeners[i].call(listeners[i], args);
        if (args.isPropagationStopped()) {
          break;
        }
      }
    }

    return args;
  };
}

export const videoEventDispatcher = new EventDispatcher();
