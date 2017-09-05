import React from 'react';
import PropTypes from 'prop-types';
import * as api from 'api';
import Favico from 'favico.js';
import Uploads from 'api/Uploads';
import { connect } from 'react-redux';
import { pmsSubscribe } from 'actions/pmsActions';
import { roomJoin, roomSend } from 'actions/roomActions';
import { layoutWindowFocused, layoutToggleUsersCollapsed, layoutSwitchActiveChat, layoutErrorMessage } from 'actions/layoutActions';
import { domOnWindowBlur } from 'utils/dom';
import { findActiveChatMessages } from 'utils/messages';
import UsersPanel from 'components/Chat/UsersPanel';
import MessagesPanel from 'components/Chat/MessagesPanel';
import MessageInput from 'components/Chat/MessageInput';

class ChatSide extends React.PureComponent {
  static propTypes = {
    roomName: PropTypes.string.isRequired
  };

  constructor(props) {
    super(props);

    this.messagesPanelRef = null;
    this.messageInputRef  = null;
    this.favicon          = null;
  }

  componentDidMount() {
    const { dispatch, settings } = this.props;

    this.favicon = new Favico();
    domOnWindowBlur((status) => {
      dispatch(layoutWindowFocused(status));
    });

    api.socket.on('socket/connect', () => {
      dispatch(layoutErrorMessage(''));
      dispatch(roomJoin(this.props.roomName));
      dispatch(pmsSubscribe());
    });
    api.socket.on('socket/disconnect', (resp) => {
      console.info(resp);
      if (resp.code === 3) {
        dispatch(layoutErrorMessage(resp.reason));
      } else if (resp.code === 6 || resp.code === 5) {
        dispatch(layoutErrorMessage('Connection to server lost.'));
      }
    });
    api.socket.connect(settings.socket.uri);
  }

  componentDidUpdate(prevProps) {
    if (prevProps.room.numNewMessages !== this.props.room.numNewMessages) {
      this.favicon.badge(this.props.room.numNewMessages);
    }
  }

  handleSendInput = (inputValue) => {
    this.props.dispatch(roomSend(inputValue));
    this.messagesPanelRef.scrollToBottom();
    this.messageInputRef.focus();
  };

  handleAttachInput = () => {
    this.messagesPanelRef.openUpload();
  };

  handleClickUser = (username) => {
    if (this.props.user.isAuthenticated && username.toLowerCase() !== this.props.user.username) {
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

  handleUpload = (file) => {
    Uploads.upload(file)
      .then((url) => {
        this.props.dispatch(roomSend(url));
        this.messagesPanelRef.scrollToBottom();
        this.messageInputRef.focus();
      }).catch((error) => {
        this.props.dispatch(layoutErrorMessage(error.toString()));
      });
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
        messages={messages}
        settings={this.props.settings}
        users={this.props.users.repo}
        onUpload={this.handleUpload}
        ref={(ref) => { this.messagesPanelRef = ref; }}
      />
    );
  }

  renderMessageInput() {
    return (
      <MessageInput
        settings={this.props.settings}
        tabComplete={this.props.room.users}
        onSend={this.handleSendInput}
        onAttach={this.handleAttachInput}
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
    user:     Object.assign({}, state.user),
    room:     Object.assign({}, state.room),
    users:    Object.assign({}, state.users),
    pms:      Object.assign({}, state.pms),
    layout:   Object.assign({}, state.layout),
    settings: Object.assign({}, state.settings)
  };
}

export default connect(mapStateToProps)(ChatSide);
