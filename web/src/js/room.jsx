import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import darkBaseTheme from 'material-ui/styles/baseThemes/darkBaseTheme';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import Room from './components/Room';

const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <MuiThemeProvider muiTheme={getMuiTheme(darkBaseTheme)}>
    <Room name={name} />
  </MuiThemeProvider>,
  mount
);
