import React from 'react';
import PropTypes from 'prop-types';

export const UserPropType = PropTypes.shape({
  username: PropTypes.string,
  avatar:   PropTypes.string,
  profile:  PropTypes.string,
  roles:    PropTypes.array
});

export const MessagePropType = PropTypes.shape({
  id:      PropTypes.number,
  date:    PropTypes.instanceOf(Date),
  from:    PropTypes.string,
  message: PropTypes.string
});

/**
 * Calls React.Children.map() recursively on the given children
 *
 * @param {*} children The children to map
 * @param {function} cb Called for each child
 * @returns {*}
 */
export function childrenRecursiveMap(children, cb) {
  return React.Children.map(children, (child) => {
    if (React.isValidElement(child) && child.props.children) {
      child = React.cloneElement(child, { // eslint-disable-line no-param-reassign
        children: childrenRecursiveMap(child.props.children, cb)
      });
    }

    return cb(child);
  });
}
