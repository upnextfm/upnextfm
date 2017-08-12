import React from 'react';
import PropTypes from 'prop-types';
import YouTube from 'react-youtube';
import { connect } from 'react-redux';
import { objectKeyFilter } from 'utils/objects';
import LoginDialog from 'components/Dialogs/LoginDialog';
import Nav from 'components/Nav';

class Room extends React.Component {
  static propTypes = {
    name:     PropTypes.string.isRequired,
    auth:     PropTypes.object,
    nav:      PropTypes.object,
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

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
        <LoginDialog />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
