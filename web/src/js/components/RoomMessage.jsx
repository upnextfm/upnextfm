import React from 'react';
import { ListItem } from 'material-ui/List';
import Moment from 'react-moment';
import RoomUser from 'components/RoomUser';

const RoomMessage = ({ message, user, ...props }) => (
  <ListItem className="up-room-message" {...props}>
    <RoomUser user={user} />
    <Moment date={message.date} format="HH:mm" className="up-room-message__date" />
    <div className="up-room-message__message">
      {message.message}
    </div>

  </ListItem>
);

export default RoomMessage;
