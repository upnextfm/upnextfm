import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import YouTube from 'react-youtube';
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
    const { auth, ...props } = this.props;
    const opts = {
      width:      '100%',
      playerVars: {
        autoplay: 0
      }
    };

    return (
      <div {...objectKeyFilter(props, Room.propTypes)}>
        <Nav auth={auth} />
        <div className="up-room">
          <div className="up-room__chat">
            Chat
          </div>
          <div className="up-room__video">
            <YouTube videoId="BC2dRkm8ATU" opts={opts} />
          </div>
        </div>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
