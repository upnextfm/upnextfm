import React from 'react';
import { MessagePropType } from 'utils/props';

const JoinMessageType = ({ message, ...props }) => (
  <li className="up-room-join-message" data-id={message.id} {...props}>
    {message.message}
  </li>
);

JoinMessageType.propTypes = {
  message: MessagePropType.isRequired
};

export default JoinMessageType;
