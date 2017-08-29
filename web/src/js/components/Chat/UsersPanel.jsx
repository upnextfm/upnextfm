import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { getNumNewMessages } from 'utils/messages';
import { usersFindByUsername } from 'utils/users';
import Hidden from 'material-ui/Hidden';
import Avatar from 'material-ui/Avatar';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import KeyboardArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import User from 'components/Chat/User';
import UserMenu from 'components/Chat/UserMenu';

export default class UsersPanel extends React.Component {
  static propTypes = {
    pms:              PropTypes.object,
    roomName:         PropTypes.string,
    roomSettings:     PropTypes.object,
    roomUsers:        PropTypes.array,
    repoUsers:        PropTypes.array,
    isCollapsed:      PropTypes.bool,
    onClickUser:      PropTypes.func,
    onCollapse:       PropTypes.func,
    onClickRoomThumb: PropTypes.func
  };

  static defaultProps = {
    pms:              {},
    roomUsers:        [],
    repoUsers:        [],
    isCollapsed:      false,
    onClickUser:      () => {},
    onCollapse:       () => {},
    onClickRoomThumb: () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      menuAnchor: undefined,
      menuOpen:   false
    };
  }

  handleClickUser = (e) => {
    const username = e.currentTarget.getAttribute('data-username').toLowerCase();
    this.props.onClickUser(username);
  };

  handleContextMenuUser = (e) => {
    e.preventDefault();
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
    const {
      pms,
      roomName,
      roomSettings,
      roomUsers,
      repoUsers,
      activeChat,
      isCollapsed,
      onCollapse,
      onClickRoomThumb,
      conversations = pms.conversations
    } = this.props;

    return (
      <div className={classNames(
        'up-room-panel__users',
        {
          'up-collapsed': isCollapsed
        }
      )}
      >
        <List>
          <ListItem className="up-block up-room-thumb" onClick={onClickRoomThumb} button>
            <div className="up-room-user">
              <Avatar src={roomSettings.thumbSm} className="up-avatar" />
              <span className="up-username">/r/{roomName}</span>
            </div>
          </ListItem>
          {roomUsers.map(username => (
            <ListItem
              key={username}
              className={classNames(
                'up-block',
                { 'up-active': username.toLowerCase() === activeChat }
              )}
              onClick={this.handleClickUser}
              onContextMenu={this.handleContextMenuUser}
              data-username={username}
              button
            >
              <User
                user={usersFindByUsername(repoUsers, username)}
                numNewMessages={getNumNewMessages(conversations, username)}
              />
            </ListItem>
          ))}
        </List>
        <Hidden xsDown>
          <div className="up-room-users__controls">
            <IconButton className="up-collapse" onClick={onCollapse}>
              <KeyboardArrowLeft className={classNames(
                'up-collapse__icon',
                {
                  'up-collapsed': isCollapsed
                }
               )}
              />
            </IconButton>
          </div>
        </Hidden>
        <UserMenu
          anchor={this.state.menuAnchor}
          isOpen={this.state.menuOpen}
          onClickProfile={this.handleClickProfile}
          onRequestClose={this.handleCloseMenu}
        />
      </div>
    );
  }
}

