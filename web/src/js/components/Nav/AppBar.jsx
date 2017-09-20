import React from 'react';
import PropTypes from 'prop-types';

const AppBar = ({ position, children, ...props }) => (
  <header style={{ position }} {...props}>
    {children}
  </header>
);

AppBar.propTypes = {
  position: PropTypes.string,
  children: PropTypes.node
};

AppBar.defaultProps = {
  position: 'static',
  children: ''
};

export default AppBar;
