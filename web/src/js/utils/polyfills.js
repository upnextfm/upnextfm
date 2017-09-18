/* eslint-disable */
window.assign = Object.assign;

import Promise from 'promise-polyfill';
if (!window.Promise) {
  window.Promise = Promise;
}
import 'whatwg-fetch';

// Disabled because it does not play nice with React 16.
// import injectTapEventPlugin from 'react-tap-event-plugin';
// injectTapEventPlugin();
