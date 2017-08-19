import React from 'react';
import { connect } from 'react-redux';
import UsersPanel from 'components/Chat/UsersPanel';
import MessagesPanel from 'components/Chat/MessagesPanel';
import MessageInput from 'components/Chat/MessageInput';

class ChatSide extends React.Component {
  render() {
    return (
      <div className="up-room-side__chat">
        <UsersPanel />
        <div className="up-room-messages">
          <MessagesPanel />
          <MessageInput />
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(ChatSide);
