import React from 'react';
import { MessagePropType } from 'utils/props';
import Parser from 'components/Parser';

const JoinMessageType = ({ message, ...props }) => (
  <li className="up-room-join-message" data-id={message.id} {...props}>
    <Parser>
      {message.message}
    </Parser>
  </li>
);

JoinMessageType.propTypes = {
  message: MessagePropType.isRequired
};

export default JoinMessageType;
