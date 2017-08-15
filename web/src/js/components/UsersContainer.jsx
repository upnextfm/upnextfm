import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import List, { ListItem } from 'material-ui/List';
import RoomUser from 'components/RoomUser';

class UsersContainer extends React.Component {
  static propTypes = {
    users: PropTypes.array
  };

  render() {
    return (
      <List className="up-room__chat__users up-paper-container">
        {this.props.users.map(user => (
          <ListItem key={user.username} button>
            <RoomUser user={user} />
          </ListItem>
        ))}
      </List>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(UsersContainer);
