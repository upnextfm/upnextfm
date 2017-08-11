import React from 'react';
import PropTypes from 'prop-types';
import { objectKeyFilter } from '../utils/objects';
import Nav from './Nav';

class Room extends React.Component {
  static propTypes = {
    name: PropTypes.string.isRequired
  };

  render() {
    const { name, ...props} = this.props;

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        <Nav />
        <p>Welcome to {name}!</p>
      </div>
    );
  }
}

export default Room;
