import React from 'react';
import { ListItem } from 'material-ui/List';
import Avatar from 'material-ui/Avatar';

const RoomUser = ({ user, ...props }) => (
  <ListItem
    className="up-room__chat__users__user"
    {...props}
    button
  >
    <Avatar src={user.avatar} alt="Avatar" className="up-avatar" aria-hidden />
    <div className="up-username">
      {user.username}
    </div>
  </ListItem>
);

export default RoomUser;
