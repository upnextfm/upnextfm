import React from 'react';
import { connect } from 'react-redux';
import Users from 'components/Users';
import Messages from 'components/Messages';
import MessageInput from 'components/MessageInput';

class ChatContainer extends React.Component {
  render() {
    return (
      <div className="up-room__chat">
        <Users />
        <div className="up-room__chat__messages-container">
          <Messages />
          <MessageInput />
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(ChatContainer);
