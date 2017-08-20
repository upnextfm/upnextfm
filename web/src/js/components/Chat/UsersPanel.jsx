import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { connect } from 'react-redux';
import { roomToggleUsersCollapsed } from 'actions/roomActions';
import { videoToggleMute } from 'actions/videoActions';
import { usersFindByUsername } from 'utils/users';
import { animateScrollTo } from 'utils/animate';
import Hidden from 'material-ui/Hidden';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import SwapHoriz from 'material-ui-icons/SwapHoriz';
import VolumeOff from 'material-ui-icons/VolumeOff';
import VolumeUp from 'material-ui-icons/VolumeUp';
import ArrowDown from 'material-ui-icons/KeyboardArrowDown';
import User from 'components/Chat/User';


class UsersPanel extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  handleClickCollapse = () => {
    this.props.dispatch(roomToggleUsersCollapsed());
  };

  handleClickMute = () => {
    this.props.dispatch(videoToggleMute());
  };

  handleClickDown = () => {
    animateScrollTo(document.body, document.body.scrollHeight, 50);
  };

  render() {
    const { room, video, users } = this.props;

    return (
      <div className={classNames(
        'up-room-panel__users',
        {
          'up-collapsed': room.isUsersCollapsed
        }
      )}
      >
        <List>
          {room.users.map(username => (
            <ListItem key={username} button>
              <User user={usersFindByUsername(users.repo, username)} />
            </ListItem>
          ))}
        </List>
        <Hidden xsDown>
          <IconButton className="up-collapse" onClick={this.handleClickCollapse}>
            <SwapHoriz />
          </IconButton>
        </Hidden>
        <Hidden smUp>
          <div className="up-room-panel__users__controls">
            <IconButton title={video.isMuted ? 'Unmute' : 'Mute'} onClick={this.handleClickMute}>
              {video.isMuted ? <VolumeOff /> : <VolumeUp />}
            </IconButton>
            <IconButton onClick={this.handleClickDown}>
              <ArrowDown />
            </IconButton>
          </div>
        </Hidden>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    room:  Object.assign({}, state.room),
    video: Object.assign({}, state.video),
    users: state.users
  };
}

export default connect(mapStateToProps)(UsersPanel);
