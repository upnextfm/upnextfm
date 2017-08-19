import React from 'react';
import Moment from 'react-moment';
import { ListItem } from 'material-ui/List';
import { UserPropType, MessagePropType } from 'utils/props';
import User from 'components/Chat/User';
import Linkify from 'components/Linkify';

const Message = ({ message, user, ...props }) => (
  <ListItem className="up-room-message" {...props}>
    <div>
      <User user={user} />
      <Moment date={message.date} format="HH:mm" className="up-room-message__date" />
    </div>
    <div className="up-room-message__body">
      <Linkify properties={{ target: '_blank' }}>
        {message.message}
      </Linkify>
    </div>
  </ListItem>
);

Message.propTypes = {
  message: MessagePropType.isRequired,
  user:    UserPropType.isRequired
};

export default Message;
