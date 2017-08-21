import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { connect } from 'react-redux';
import { roomToggleUsersCollapsed } from 'actions/roomActions';
import { usersFindByUsername } from 'utils/users';
import Hidden from 'material-ui/Hidden';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import KeyboardArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import User from 'components/Chat/User';

class UsersPanel extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  handleClickCollapse = () => {
    this.props.dispatch(roomToggleUsersCollapsed());
  };

  render() {
    const { room, users } = this.props;

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
            <KeyboardArrowLeft />
          </IconButton>
        </Hidden>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    room:  Object.assign({}, state.room),
    users: state.users
  };
}

export default connect(mapStateToProps)(UsersPanel);
