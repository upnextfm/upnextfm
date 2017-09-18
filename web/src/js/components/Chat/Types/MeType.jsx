import React from 'react';
import { UserPropType, MessagePropType } from 'utils/props';

const MeType = ({ message, user, ...props }) => (
  <li className="up-room-me" data-id={message.id} data-date={message.date} data-user={user.username} {...props}>
    {user.username} {message.message}
  </li>
);

MeType.propTypes = {
  message: MessagePropType.isRequired,
  user:    UserPropType.isRequired
};

export default MeType;
