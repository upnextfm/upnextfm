import React from 'react';
import PropTypes from 'prop-types';
import Favico from 'favico.js';
import { connect } from 'react-redux';
import { roomJoin } from 'actions/roomActions';
import { layoutWindowFocused } from 'actions/layoutActions';
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
    if (prevProps.numNewMessages !== this.props.numNewMessages) {
      this.favicon.badge(this.props.numNewMessages);
    }
  }

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
