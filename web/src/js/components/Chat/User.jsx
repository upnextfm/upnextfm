import React from 'react';
import PropTypes from 'prop-types';
import { UserPropType } from 'utils/props';
import Avatar from 'material-ui/Avatar';

const User = ({ user, numNewMessages, ...props }) => (
  <div className="up-room-user" {...props}>
    <Avatar src={user.avatar} alt="Avatar" className="up-avatar" aria-hidden />
    <div className="up-username">
      {user.username}
    </div>
    {numNewMessages === 0 ? null : (
      <div className="up-badge" title="New Messages">
        {numNewMessages < 99 ? numNewMessages : '99+'}
      </div>
    )}
  </div>
);

User.propTypes = {
  user:           UserPropType.isRequired,
  numNewMessages: PropTypes.number
};

export default User;
