import React from 'react';
import PropTypes from 'prop-types';
import { Scrollbars } from 'react-custom-scrollbars';
import { usersFindByUsername } from 'utils/users';
import List from 'material-ui/List';
import MessageType from 'components/Chat/Types/MessageType';
import NoticeType from 'components/Chat/Types/NoticeType';

export default class MessagesPanel extends React.Component {
  static propTypes = {
    messages: PropTypes.array,
    users:    PropTypes.array,
    settings: PropTypes.object
  };

  shouldComponentUpdate(nextProps) {
    return (
      nextProps.messages.length !== this.props.messages.length ||
      nextProps.users.length !== this.props.users.length
    );
  }

  componentDidUpdate(prevProps) {
    if (prevProps.messages.length !== this.props.messages.length) {
      setTimeout(() => {
        this.scrollRef.scrollToBottom();
      }, 100);
    }
  }

  render() {
    const { messages, users, settings } = this.props;
    let prevUser = null;
    let prevMessage = null;

    return (
      <Scrollbars ref={(ref) => { this.scrollRef = ref; }}>
        <List className="up-room-panel__messages">
          {messages.map((message) => {
            let item;
            const user = usersFindByUsername(users, message.from);
            if (message.type === 'message') {
              item = (
                <MessageType
                  key={message.id}
                  message={message}
                  user={user}
                  prevMessage={prevMessage}
                  prevUser={prevUser}
                />
              );
            } else if (message.type === 'notice' && settings.user.showNotices) {
              item = (
                <NoticeType
                  key={message.id}
                  message={message}
                  user={user}
                />
              );
            }

            prevUser    = user;
            prevMessage = message;
            return item;
          })}
        </List>
      </Scrollbars>
    );
  }
}

