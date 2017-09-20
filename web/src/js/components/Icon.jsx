import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';

const Icon = ({ name, className, ...props }) => (
  <i className={classNames('material-icons', className)} {...props}>
    {name}
  </i>
);

Icon.propTypes = {
  name:      PropTypes.string.isRequired,
  className: PropTypes.string
};

Icon.defaultProps = {
  className: ''
};

export default Icon;
