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
    users: PropTypes.array
  };

  render() {
    const { room, users } = this.props;

    return (
      <List className="up-room__chat__users up-paper-container">
        {room.users.map(username => (
          <ListItem key={username} button>
            <RoomUser user={usersFindByUsername(users, username)} />
          </ListItem>
        ))}
      </List>
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
