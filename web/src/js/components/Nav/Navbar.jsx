import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const Navbar = ({ className, children, ...props }) => (
  <nav className={classNames('up-nav', className)} {...props}>
    {children}
  </nav>
);

Navbar.propTypes = {
  children:  PropTypes.node,
  className: PropTypes.string
};

Navbar.defaultProps = {
  children:  '',
  className: ''
};

export default Navbar;
