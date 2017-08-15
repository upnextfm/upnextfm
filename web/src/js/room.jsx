import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import Moment from 'react-moment';
import { MuiThemeProvider, createMuiTheme } from 'material-ui/styles';
import createPalette from 'material-ui/styles/palette';
import orange from 'material-ui/colors/orange';
import store from 'store/store';
import Room from 'components/Room';

Moment.startPooledTimer();

const theme = createMuiTheme({
  palette: createPalette({
    type:    'dark',
    primary: orange
  })
});
const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <Provider store={store}>
    <MuiThemeProvider theme={theme}>
      <Room name={name} />
    </MuiThemeProvider>
  </Provider>
  ,
  mount
);
