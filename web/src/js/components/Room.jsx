import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { objectKeyFilter } from '../utils/objects';
import Nav from './Nav';

class Room extends React.Component {
  static propTypes = {
    name:     PropTypes.string.isRequired,
    dispatch: PropTypes.func
  };

  render() {
    const { name, ...props } = this.props;

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        <Nav />
        <p>Welcome to {name}!</p>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.auth);
}

export default connect(mapStateToProps)(Room);
