import React from 'react';
import { UserPropType, MessagePropType } from 'utils/props';


const Notice = ({ message, user, ...props }) => (
  <li className="up-room-notice" data-id={message.id} data-date={message.date} data-user={user.username} {...props}>
    {message.message}
  </li>
);

Notice.propTypes = {
  message: MessagePropType.isRequired,
  user:    UserPropType.isRequired
};

export default Notice;
