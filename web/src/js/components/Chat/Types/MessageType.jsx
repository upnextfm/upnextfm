import React from 'react';
import PropTypes from 'prop-types';
import Moment from 'react-moment';
import { UserPropType, MessagePropType } from 'utils/props';
import Linkify from 'components/Linkify';
import Avatar from 'material-ui/Avatar';

const MessageType = ({ message, user, prevMessage, prevUser, onClickUser, ...props }) => {
  const showUser = (
    (!prevUser || prevUser.username !== user.username) ||
    (!prevMessage || message.date - prevMessage.date > 60000)
  );

  return (
    <li className="up-room-message" data-id={message.id} data-date={message.date} {...props}>
      {!showUser ? null : (
        <div className="up-room-message__user">
          <Avatar src={user.avatar} alt="Avatar" className="up-avatar"  data-username={user.username} onClick={onClickUser} aria-hidden />
          <div className="up-username">
            {user.username}
          </div>
          <Moment date={message.date} className="up-room-message__date" fromNow />
        </div>
      )}
      <div className="up-room-message__body">
        <Linkify properties={{ target: '_blank' }}>
          {message.message}
        </Linkify>
      </div>
    </li>
  );
};

MessageType.propTypes = {
  message:     MessagePropType.isRequired,
  user:        UserPropType.isRequired,
  prevMessage: MessagePropType,
  prevUser:    UserPropType,
  onClickUser: PropTypes.func
};

export default MessageType;
