import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import Room from './components/Room';

const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <MuiThemeProvider>
    <Room name={name} />
  </MuiThemeProvider>,
  mount
);
