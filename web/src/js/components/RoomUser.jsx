import React from 'react';
import Avatar from 'material-ui/Avatar';

const RoomUser = ({ user, ...props }) => (
  <div className="up-room-user" {...props}>
    <Avatar src={user.avatar} alt="Avatar" className="up-avatar" aria-hidden />
    <div className="up-username">
      {user.username}
    </div>
  </div>
);

export default RoomUser;
