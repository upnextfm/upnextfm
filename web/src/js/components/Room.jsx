import React from 'react';
import PropTypes from 'prop-types';
import { objectKeyFilter } from '../utils/objects';

class Room extends React.Component {
  static propTypes = {
    name: PropTypes.string.isRequired
  };

  render() {
    const { name, ...props} = this.props;

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        Welcome to {name}!
      </div>
    );
  }
}

export default Room;
