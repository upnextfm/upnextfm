import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import createMuiTheme from 'material-ui/styles/theme';
import { MuiThemeProvider } from 'material-ui/styles';
import Room from './components/Room';

const theme = createMuiTheme();
const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <MuiThemeProvider theme={theme}>
    <Room name={name} />
  </MuiThemeProvider>,
  mount
);
