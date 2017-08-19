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

  componentDidUpdate() {
    this.scrollRef.scrollToBottom();
  }

  render() {
    const { room, users } = this.props;

    return (
      <Scrollbars ref={(ref) => { this.scrollRef = ref; }}>
        <List className="up-room-panel__messages">
          {room.messages.map(message => (
            <Message
              key={message.id}
              message={message}
              user={usersFindByUsername(users.repo, message.from)}
            />
          ))}
        </List>
      </Scrollbars>
    );
  }
}

function mapStateToProps(state) {
  return {
    room:  Object.assign({}, state.room),
    users: state.users
  };
}

export default connect(mapStateToProps)(MessagesPanel);
