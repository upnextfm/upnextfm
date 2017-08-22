import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { usersFindByUsername } from 'utils/users';
import List from 'material-ui/List';
import Message from 'components/Chat/Message';

class MessagesPanel extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  componentDidMount() {
    setTimeout(() => {
      this.scrollRef.scrollToBottom();
    }, 1000);
  }

  componentDidUpdate(prevProps) {
    if (prevProps.room.messages.length !== this.props.room.messages.length) {
      this.scrollRef.scrollToBottom();
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
            const user = usersFindByUsername(users.repo, message.from);
            const item = (
              <Message
                key={message.id}
                message={message}
                user={user}
                prevMessage={prevMessage}
                prevUser={prevUser}
              />
            );
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
