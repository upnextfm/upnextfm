import React from 'react';
import { UserPropType, MessagePropType } from 'utils/props';


const NoticeType = ({ message, user, ...props }) => (
  <li className="up-room-notice" data-id={message.id} data-date={message.date} data-user={user.username} {...props}>
    {message.message}
  </li>
);

NoticeType.propTypes = {
  message: MessagePropType.isRequired,
  user:    UserPropType.isRequired
};

export default NoticeType;
