import 'utils/polyfills';
import React from 'react';
import ReactDOM from 'react-dom';
import Room from './components/Room';

const mount = document.getElementById('mount');
const name  = mount.getAttribute('data-room');

ReactDOM.render(
  <Room name={name} />,
  mount
);
