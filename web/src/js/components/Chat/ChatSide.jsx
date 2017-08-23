import React from 'react';
import PropTypes from 'prop-types';
import Favico from 'favico.js';
import { connect } from 'react-redux';
import { roomJoin, roomSend, roomInputChange } from 'actions/roomActions';
import { layoutWindowFocused, layoutToggleUsersCollapsed } from 'actions/layoutActions';
import { domOnWindowBlur } from 'utils/dom';
import * as api from 'api';
import UsersPanel from 'components/Chat/UsersPanel';
import MessagesPanel from 'components/Chat/MessagesPanel';
import MessageInput from 'components/Chat/MessageInput';

class ChatSide extends React.Component {
  static propTypes = {
    roomName:  PropTypes.string.isRequired,
    socketURI: PropTypes.string.isRequired
  };

  constructor(props) {
    super(props);
    this.favicon = null;
  }

  componentDidMount() {
    this.favicon = new Favico();
    domOnWindowBlur((status) => {
      this.props.dispatch(layoutWindowFocused(status));
    });

    api.socket.connect(this.props.socketURI)
      .then(() => {
        this.props.dispatch(roomJoin(this.props.roomName));
      });
  }

  componentDidUpdate(prevProps) {
    if (prevProps.room.numNewMessages !== this.props.room.numNewMessages) {
      this.favicon.badge(this.props.room.numNewMessages);
    }
  }

  handleSendInput = () => {
    this.props.dispatch(roomSend());
  };

  handleChangeInput = (value) => {
    this.props.dispatch(roomInputChange(value));
  };

  handleCollapseUsers = () => {
    this.props.dispatch(layoutToggleUsersCollapsed());
  };

  renderUsersPanel() {
    return (
      <UsersPanel
        onCollapse={this.handleCollapseUsers}
        roomUsers={this.props.room.users}
        repoUsers={this.props.users.repo}
        isCollapsed={this.props.layout.isUsersCollapsed}
      />
    );
  }

  renderMessagesPanel() {
    return (
      <MessagesPanel
        settings={this.props.settings}
        messages={this.props.room.messages}
        users={this.props.users.repo}
      />
    );
  }

  renderMessageInput() {
    return (
      <MessageInput
        value={this.props.room.inputValue}
        tabComplete={this.props.room.users}
        onSend={this.handleSendInput}
        onChange={this.handleChangeInput}
      />
    );
  }

  render() {
    return (
      <div className="up-room-side__chat">
        {this.renderUsersPanel()}
        <div className="up-room-messages">
          {this.renderMessagesPanel()}
          {this.renderMessageInput()}
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    room:     Object.assign({}, state.room),
    users:    Object.assign({}, state.users),
    layout:   Object.assign({}, state.layout),
    settings: Object.assign({}, state.settings)
  };
}

export default connect(mapStateToProps)(ChatSide);
