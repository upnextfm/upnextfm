import React from 'react';
import { ListItem } from 'material-ui/List';
import RoomUser from 'components/RoomUser';

const RoomMessage = ({ message, ...props }) => (
  <ListItem className="up-room-message" {...props}>
    <RoomUser user={message.user} />
    <div className="up-room-message__message">
      {message.message}
    </div>
  </ListItem>
);

export default RoomMessage;
