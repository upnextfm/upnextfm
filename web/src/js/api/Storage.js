export default class Storage {
  /**
   * @param {string} key
   * @param {*} value
   */
  static setItem(key, value) {
    localStorage.setItem(key, JSON.stringify(value));
  }

  /**
   * @param {string} key
   * @param {*} def
   */
  static getItem(key, def = null) {
    let value = localStorage.getItem(key);
    if (value !== null && value !== 'null' && value !== undefined && value !== 'undefined') {
      value = JSON.parse(value);
    } else {
      value = def;
    }

    return value;
  }

  /**
   * @param {string} key
   */
  static removeItem(key) {
    localStorage.removeItem(key);
  }
}

