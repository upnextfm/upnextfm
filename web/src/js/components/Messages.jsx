import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { usersFindByUsername } from 'utils/users';
import List from 'material-ui/List';
import RoomMessage from 'components/RoomMessage';

class Messages extends React.Component {
  static propTypes = {
    room:  PropTypes.object,
    users: PropTypes.object
  };

  render() {
    const { room, users } = this.props;

    return (
      <List className="up-room__chat__messages">
        {room.messages.map(message => (
          <RoomMessage
            key={message.id}
            message={message}
            user={usersFindByUsername(users.repo, message.from)}
          />
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

export default connect(mapStateToProps)(Messages);
