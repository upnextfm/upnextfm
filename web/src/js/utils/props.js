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
