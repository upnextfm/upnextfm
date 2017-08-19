import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { usersFindByUsername } from 'utils/users';
import List, { ListItem } from 'material-ui/List';
import Hidden from 'material-ui/Hidden';
import RoomUser from 'components/RoomUser';

class Users extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  render() {
    const { room, users } = this.props;

    return (
      <Hidden xsDown>
        <List className="up-room__chat__users">
          {room.users.map(username => (
            <ListItem key={username} button>
              <RoomUser user={usersFindByUsername(users.repo, username)} />
            </ListItem>
          ))}
        </List>
      </Hidden>
    );
  }
}

function mapStateToProps(state) {
  return {
    room:  Object.assign({}, state.room),
    users: state.users
  };
}

export default connect(mapStateToProps)(Users);
