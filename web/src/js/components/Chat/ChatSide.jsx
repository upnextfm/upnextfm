import React from 'react';
import PropTypes from 'prop-types';
import * as api from 'api';
import Favico from 'favico.js';
import { connect } from 'react-redux';
import { pmsSubscribe } from 'actions/pmsActions';
import { roomJoin, roomSend, roomInputChange } from 'actions/roomActions';
import { layoutWindowFocused, layoutToggleUsersCollapsed, layoutSwitchActiveChat } from 'actions/layoutActions';
import { domOnWindowBlur } from 'utils/dom';
import { findActiveChatMessages } from 'utils/messages';
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

    this.messagesPanelRef = null;
    this.messageInputRef  = null;
    this.favicon          = null;
  }

  componentDidMount() {
    this.favicon = new Favico();
    domOnWindowBlur((status) => {
      this.props.dispatch(layoutWindowFocused(status));
    });

    api.socket.on('socket/connect', () => {
      this.props.dispatch(roomJoin(this.props.roomName));
      this.props.dispatch(pmsSubscribe());
    });
    api.socket.connect(this.props.socketURI);
  }

  componentDidUpdate(prevProps) {
    if (prevProps.room.numNewMessages !== this.props.room.numNewMessages) {
      this.favicon.badge(this.props.room.numNewMessages);
    }
  }

  handleSendInput = () => {
    this.props.dispatch(roomSend(this.props.room.inputValue));
    this.messageInputRef.focus();
  };

  handleChangeInput = (value) => {
    this.props.dispatch(roomInputChange(value));
  };

  handleClickUser = (username) => {
    if (this.props.auth.isAuthenticated && username.toLowerCase() !== this.props.auth.username) {
      this.props.dispatch(layoutSwitchActiveChat(username));
      this.messageInputRef.focus();
    }
  };

  handleCollapseUsers = () => {
    this.props.dispatch(layoutToggleUsersCollapsed());
    this.messagesPanelRef.scrollToBottom();
    this.messageInputRef.focus();
  };

  handleClickRoomThumb = () => {
    this.props.dispatch(layoutSwitchActiveChat('room'));
    this.messageInputRef.focus();
  };

  renderUsersPanel() {
    return (
      <UsersPanel
        onClickUser={this.handleClickUser}
        onCollapse={this.handleCollapseUsers}
        onClickRoomThumb={this.handleClickRoomThumb}
        pms={this.props.pms}
        roomName={this.props.roomName}
        roomSettings={this.props.settings.room}
        roomUsers={this.props.room.users}
        repoUsers={this.props.users.repo}
        activeChat={this.props.layout.activeChat}
        isCollapsed={this.props.layout.isUsersCollapsed}
      />
    );
  }

  renderMessagesPanel() {
    const messages = findActiveChatMessages(
      this.props.layout.activeChat,
      this.props.room.messages,
      this.props.pms.conversations
    );

    return (
      <MessagesPanel
        settings={this.props.settings}
        messages={messages}
        users={this.props.users.repo}
        ref={(ref) => { this.messagesPanelRef = ref; }}
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
        ref={(ref) => { this.messageInputRef = ref; }}
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
    auth:     Object.assign({}, state.auth),
    room:     Object.assign({}, state.room),
    users:    Object.assign({}, state.users),
    pms:      Object.assign({}, state.pms),
    layout:   Object.assign({}, state.layout),
    settings: Object.assign({}, state.settings)
  };
}

export default connect(mapStateToProps)(ChatSide);
