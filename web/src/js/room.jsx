import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { MuiThemeProvider } from 'material-ui/styles';
import createMuiTheme from 'material-ui/styles/theme';
import store from './store/store';
import Room from './components/Room';

const theme = createMuiTheme();
const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <MuiThemeProvider theme={theme}>
    <Provider store={store}>
      <Room name={name} />
    </Provider>
  </MuiThemeProvider>
  ,
  mount
);
