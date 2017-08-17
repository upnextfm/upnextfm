import * as endpoints from 'api/endpoints';

const TOKEN_KEY     = 'token';
const REFRESH_KEY   = 'refresh_token';
const USERNAME_KEY  = 'username';
const RE_LOGIN_SECS = 120;

class Auth {

  /**
   * Constructor
   */
  constructor() {
    this.creds     = null;
    this.interval  = null;
    this.listeners = [];
    this.refresh();
  }

  /**
   *
   * @param {Function} listener
   */
  listen(listener) {
    this.listeners.push(listener);
  }

  /**
   *
   * @param {Function} listener
   */
  unlisten(listener) {
    const index = this.listeners.indexOf(listener);
    if (index !== -1) {
      this.listeners.splice(index, 1);
    }
  }

  /**
   *
   */
  trigger() {
    this.listeners.forEach((listener) => {
      listener(this.isAuthenticated());
    });
  }

  /**
   *
   * @returns {boolean}
   */
  isAuthenticated() {
    return localStorage.getItem(TOKEN_KEY) !== null;
  }

  /**
   * @returns {string|null}
   */
  getToken() {
    return localStorage.getItem(TOKEN_KEY);
  }

  /**
   * @returns {string|null}
   */
  getRefreshToken() {
    return localStorage.getItem(REFRESH_KEY);
  }

  getUsername() {
    return localStorage.getItem(USERNAME_KEY);
  }

  /**
   *
   * @returns {*}
   */
  refresh() {
    if (this.creds !== null) {
      return this.login(this.creds);
    }

    const token = this.getRefreshToken();
    if (token) {
      const config = {
        method:  'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `refresh_token=${token}`
      };

      return fetch(endpoints.REFRESH_TOKEN, config)
        .then(resp => resp.json())
        .then((resp) => {
          if (!resp.token || !resp.refresh_token) {
            this.logout();
            return Promise.reject(resp.message);
          }

          localStorage.setItem(TOKEN_KEY, resp.token);
          localStorage.setItem(REFRESH_KEY, resp.refresh_token);
          this.startInterval();
          this.trigger();

          return resp;
        })
        .catch((error) => {
          this.logout();
          throw error;
        });
    }

    return null;
  }

  /**
   *
   * @param {{username: string, password: string}} creds
   * @returns {*|Promise.<T>}
   */
  login(creds) {
    const config = {
      method:  'POST',
      headers: {
        'Accept':       'application/json',
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `username=${encodeURIComponent(creds.username)}&password=${encodeURIComponent(creds.password)}`
    };

    return fetch(endpoints.LOGIN, config)
      .then(resp => resp.json())
      .then((resp) => {
        if (!resp.token || !resp.refresh_token) {
          this.logout();
          return Promise.reject(resp.message);
        }

        this.creds = creds;
        localStorage.setItem(TOKEN_KEY, resp.token);
        localStorage.setItem(REFRESH_KEY, resp.refresh_token);
        localStorage.setItem(USERNAME_KEY, creds.username);
        this.startInterval();
        this.trigger();

        return resp;
      })
      .catch((error) => {
        this.logout();
        throw error;
      });
  }

  /**
   *
   * @returns {boolean}
   */
  logout() {
    this.creds = null;
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(REFRESH_KEY);
    localStorage.removeItem(USERNAME_KEY);
    this.stopInterval();
    this.trigger();

    return true;
  }

  /**
   *
   * @param {{username: string, email: string, password: string}} details
   * @returns {*|Promise.<T>}
   */
  register(details) {
    const config = {
      method:  'POST',
      headers: {
        'Accept':       'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(details)
    };

    let ok = false;
    return fetch(endpoints.REGISTER, config)
      .then((resp) => {
        ok = resp.ok;
        return resp.json();
      })
      .then((resp) => {
        if (!ok) {
          throw Error(resp.error);
        }

        this.creds = {
          username: details.username,
          password: details.password
        };
        localStorage.setItem(TOKEN_KEY, resp.token);
        localStorage.setItem(REFRESH_KEY, resp.refresh_token);
        localStorage.setItem(USERNAME_KEY, details.username);
        this.startInterval();
        this.trigger();

        return resp;
      })
      .catch((error) => {
        this.logout();
        throw error;
      });
  }

  /**
   *
   */
  startInterval() {
    this.stopInterval();
    this.interval = setInterval(this.refresh.bind(this), RE_LOGIN_SECS * 1000);
  }

  /**
   *
   */
  stopInterval() {
    if (this.interval) {
      clearInterval(this.interval);
      this.interval = null;
    }
  }
}

export default new Auth();
