import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { MuiThemeProvider, createMuiTheme } from 'material-ui/styles';
import createPalette from 'material-ui/styles/palette';
import store from 'store/store';
import Room from 'components/Room';

const theme = createMuiTheme({
  palette: createPalette({
    type: 'dark'
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
