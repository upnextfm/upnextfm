import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import List from 'material-ui/List';
import RoomMessage from 'components/RoomMessage';

class Messages extends React.Component {
  static propTypes = {
    messages: PropTypes.array
  };

  render() {
    return (
      <List className="up-room__chat__messages up-paper-container">
        {this.props.messages.map(message => (
          <RoomMessage key={message.id} message={message} />
        ))}
      </List>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.room);
}

export default connect(mapStateToProps)(Messages);
