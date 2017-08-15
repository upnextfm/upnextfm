import React from 'react';
import { UserPropType } from 'utils/props';
import Avatar from 'material-ui/Avatar';

const RoomUser = ({ user, ...props }) => (
  <div className="up-room-user" {...props}>
    <Avatar src={user.avatar} alt="Avatar" className="up-avatar" aria-hidden />
    <div className="up-username">
      {user.username}
    </div>
  </div>
);

RoomUser.propTypes = {
  user: UserPropType.isRequired
};

export default RoomUser;
