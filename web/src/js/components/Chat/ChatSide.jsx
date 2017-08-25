import React from 'react';
import PropTypes from 'prop-types';
import Favico from 'favico.js';
import { connect } from 'react-redux';
import { pmsSubscribe, pmsSend } from 'actions/pmsActions';
import { roomJoin, roomSend, roomInputChange } from 'actions/roomActions';
import { layoutWindowFocused, layoutToggleUsersCollapsed } from 'actions/layoutActions';
import { domOnWindowBlur } from 'utils/dom';
import * as api from 'api';
import UsersPanel from 'components/Chat/UsersPanel';
import MessagesPanel from 'components/Chat/MessagesPanel';
import MessageInput from 'components/Chat/MessageInput';

function getMessages(activeChat, roomMessages, conversations) {
  if (activeChat === 'room') {
    return roomMessages;
  }
  for (let i = 0; i < conversations.length; i++) {
    if (conversations[i].from === activeChat) {
      return conversations[i].messages;
    }
  }
  return [];
}

class ChatSide extends React.Component {
  static propTypes = {
    roomName:  PropTypes.string.isRequired,
    socketURI: PropTypes.string.isRequired
  };

  constructor(props) {
    super(props);

    this.messagesPanelRef = null;
    this.favicon          = null;
  }

  componentDidMount() {
    this.favicon = new Favico();
    domOnWindowBlur((status) => {
      this.props.dispatch(layoutWindowFocused(status));
    });

    api.socket.connect(this.props.socketURI)
      .then(() => {
        this.props.dispatch(roomJoin(this.props.roomName));
        this.props.dispatch(pmsSubscribe());
      });
  }

  componentDidUpdate(prevProps) {
    if (prevProps.room.numNewMessages !== this.props.room.numNewMessages) {
      this.favicon.badge(this.props.room.numNewMessages);
    }
  }

  handleSendInput = () => {
    const { inputValue } = this.props.room;

    if (inputValue.indexOf('/pm') === 0) {
      const [cmd, toUsername, ...messageParts] = inputValue.split(' ');
      const message = messageParts.join(' ');
      this.props.dispatch(roomInputChange(''));
      this.props.dispatch(pmsSend(toUsername, message));
    } else {
      this.props.dispatch(roomSend());
    }
  };

  handleChangeInput = (value) => {
    this.props.dispatch(roomInputChange(value));
  };

  handleCollapseUsers = () => {
    this.props.dispatch(layoutToggleUsersCollapsed());
    this.messagesPanelRef.scrollToBottom();
  };

  renderUsersPanel() {
    return (
      <UsersPanel
        onCollapse={this.handleCollapseUsers}
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
    const messages = getMessages(
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
    pms:      Object.assign({}, state.pms),
    layout:   Object.assign({}, state.layout),
    settings: Object.assign({}, state.settings)
  };
}

export default connect(mapStateToProps)(ChatSide);
