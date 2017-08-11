import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { objectKeyFilter } from '../utils/objects';
import { login } from '../actions/authActions';
import Nav from './Nav';

class Room extends React.Component {
  static propTypes = {
    name:     PropTypes.string.isRequired,
    auth:     PropTypes.object,
    dispatch: PropTypes.func
  };

  componentDidMount() {
    this.props.dispatch(login({ username: 'headzoo', password: '123456' }));
  }

  render() {
    const { name, auth, ...props } = this.props;

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        <Nav auth={auth} />
        <p>Welcome to {name}!</p>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
