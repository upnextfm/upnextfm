import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { usersFindByUsername } from 'utils/users';
import List from 'material-ui/List';
import Message from 'components/Chat/Message';
import Notice from 'components/Chat/Notice';

class MessagesPanel extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  componentDidUpdate(prevProps) {
    if (prevProps.room.messages.length !== this.props.room.messages.length) {
      setTimeout(() => {
        this.scrollRef.scrollToBottom();
      }, 100);
    }
  }

  render() {
    const { room, users } = this.props;
    let prevUser = null;
    let prevMessage = null;

    return (
      <Scrollbars ref={(ref) => { this.scrollRef = ref; }}>
        <List className="up-room-panel__messages">
          {room.messages.map((message) => {
            let item;
            const user = usersFindByUsername(users.repo, message.from);
            if (message.type === 'message') {
              item = (
                <Message
                  key={message.id}
                  message={message}
                  user={user}
                  prevMessage={prevMessage}
                  prevUser={prevUser}
                />
              );
            } else if (message.type === 'notice') {
              item = (
                <Notice
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

function mapStateToProps(state) {
  return {
    room:  Object.assign({}, state.room),
    users: Object.assign({}, state.users)
  };
}

export default connect(mapStateToProps)(MessagesPanel);
