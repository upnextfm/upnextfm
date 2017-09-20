import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const Button = ({ fab, className, children, ...props }) => (
  <button
    className={classNames(
      'waves-effect waves-light up-btn btn',
      {
        'btn-floating': fab
      },
      className
    )}
    {...props}
  >
    {children}
  </button>
);

Button.propTypes = {
  fab:       PropTypes.bool,
  children:  PropTypes.node,
  className: PropTypes.string
};

Button.defaultProps = {
  fab:       false,
  children:  '',
  className: ''
};

export default Button;
