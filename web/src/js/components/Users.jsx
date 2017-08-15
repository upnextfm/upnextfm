import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import List, { ListItem } from 'material-ui/List';
import Hidden from 'material-ui/Hidden';
import RoomUser from 'components/RoomUser';

class Users extends React.Component {
  static propTypes = {
    users: PropTypes.array
  };

  render() {
    return (
      <Hidden xsDown>
        <List className="up-room__chat__users up-paper-container">
          {this.props.users.map(user => (
            <ListItem key={user.username} button>
              <RoomUser user={user} />
            </ListItem>
          ))}
        </List>
      </Hidden>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(Users);
