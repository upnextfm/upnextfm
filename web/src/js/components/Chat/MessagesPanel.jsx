import React from 'react';
import PropTypes from 'prop-types';
import { Scrollbars } from 'react-custom-scrollbars';
import { usersFindByUsername } from 'utils/users';
import List from 'material-ui/List';
import UserMenu from 'components/Chat/UserMenu';
import MessageType from 'components/Chat/Types/MessageType';
import NoticeType from 'components/Chat/Types/NoticeType';

export default class MessagesPanel extends React.Component {
  static propTypes = {
    messages: PropTypes.array,
    users:    PropTypes.array,
    settings: PropTypes.object
  };

  constructor(props) {
    super(props);
    this.state = {
      menuAnchor: undefined,
      menuOpen:   false
    };
  }

  shouldComponentUpdate(nextProps, nextState) {
    return (
      nextProps.messages.length !== this.props.messages.length ||
      nextProps.users.length !== this.props.users.length ||
      nextState !== this.state
    );
  }

  componentDidUpdate(prevProps) {
    if (prevProps.messages.length !== this.props.messages.length) {
      this.scrollToBottom();
    }
  }

  scrollToBottom = () => {
    setTimeout(() => {
      this.scrollRef.scrollToBottom();
    }, 10);
  };

  handleClickUser = (e) => {
    this.setState({
      menuOpen:   true,
      menuAnchor: e.currentTarget
    });
  };

  handleCloseMenu = () => {
    this.setState({ menuOpen: false });
  };

  handleClickProfile = () => {
    const username = this.state.menuAnchor.getAttribute('data-username');
    if (username) {
      window.open(`/u/${username}`);
      this.setState({ menuOpen: false });
    }
  };

  render() {
    const { messages, users, settings } = this.props;
    let prevUser = null;
    let prevMessage = null;

    return (
      <Scrollbars ref={(ref) => { this.scrollRef = ref; }}>
        <List className="up-room-panel__messages">
          {messages.map((message) => {
            let item;
            const user = usersFindByUsername(users, message.from);
            if (message.type === 'message') {
              item = (
                <MessageType
                  key={message.id}
                  user={user}
                  message={message}
                  onClickUser={this.handleClickUser}
                  prevMessage={prevMessage}
                  prevUser={prevUser}
                />
              );
            } else if (message.type === 'notice' && settings.user.showNotices) {
              item = (
                <NoticeType
                  key={message.id}
                  user={user}
                  message={message}
                />
              );
            }

            prevUser    = user;
            prevMessage = message;
            return item;
          })}
        </List>
        <UserMenu
          anchor={this.state.menuAnchor}
          isOpen={this.state.menuOpen}
          onClickProfile={this.handleClickProfile}
          onRequestClose={this.handleCloseMenu}
        />
      </Scrollbars>
    );
  }
}

