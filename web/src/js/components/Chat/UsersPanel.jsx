import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { usersFindByUsername } from 'utils/users';
import Hidden from 'material-ui/Hidden';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import KeyboardArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import User from 'components/Chat/User';
import UserMenu from 'components/Chat/UserMenu';

function getNumNewMessages(conversations, fromUsername) {
  for (let i = 0; i < conversations.length; i++) {
    if (conversations[i].from === fromUsername) {
      return conversations[i].numNewMessages;
    }
  }

  return 0;
}

export default class UsersPanel extends React.Component {
  static propTypes = {
    pms:         PropTypes.object,
    roomUsers:   PropTypes.array,
    repoUsers:   PropTypes.array,
    isCollapsed: PropTypes.bool,
    onCollapse:  PropTypes.func
  };

  static defaultProps = {
    pms:         {},
    roomUsers:   [],
    repoUsers:   [],
    isCollapsed: false,
    onCollapse:  () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      menuAnchor: undefined,
      menuOpen:   false
    };
  }

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
    const {
      pms,
      roomUsers,
      repoUsers,
      activeChat,
      isCollapsed,
      onCollapse,
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
          <ListItem className="up-block" button>
            <div className="up-room-user">
              <span>/r/lobby</span>
            </div>
          </ListItem>
          {roomUsers.map(username => (
            <ListItem
              key={username}
              className={classNames(
                'up-block',
                { 'up-active': username === activeChat }
              )}
              onClick={this.handleClickUser}
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

