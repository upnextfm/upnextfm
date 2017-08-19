import React from 'react';
import { UserPropType } from 'utils/props';
import Avatar from 'material-ui/Avatar';

const User = ({ user, ...props }) => (
  <div className="up-room-user" {...props}>
    <Avatar src={user.avatar} alt="Avatar" className="up-avatar" aria-hidden />
    <div className="up-username">
      {user.username}
    </div>
  </div>
);

User.propTypes = {
  user: UserPropType.isRequired
};

export default User;
