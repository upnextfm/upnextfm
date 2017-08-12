import React from 'react';
import PropTypes from 'prop-types';

class ChatContainer extends React.Component {
  render() {
    return (
      <div className="up-room__chat">
        <div className="up-room__chat__users">
          Users
        </div>
        <div className="up-room__chat__messages">
          <div className="up-room__chat__scroll">
            Messages
          </div>
          <div className="up-room__chat__input">
            Input
          </div>
        </div>

      </div>
    );
  }
}

export default ChatContainer;
