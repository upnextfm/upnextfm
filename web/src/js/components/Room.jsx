import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Grid from 'material-ui/Grid';
import * as api from 'api';
import { roomJoin } from 'actions/roomActions';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import Nav from 'components/Nav';
import ChatContainer from 'components/ChatContainer';
import VideoContainer from 'components/VideoContainer';

class Room extends React.Component {
  static propTypes = {
    name:      PropTypes.string.isRequired,
    socketURI: PropTypes.string.isRequired,
    auth:      PropTypes.object.isRequired,
    dispatch:  PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  componentDidMount() {
    api.socket.connect(this.props.socketURI)
      .then(() => {
        this.props.dispatch(roomJoin(this.props.name));
      });
  }

  render() {
    const { auth } = this.props;

    return (
      <div>
        <Nav auth={auth} />
        <div className="up-room">
          <Grid item xs={12} sm={12} md={7}>
            <ChatContainer />
          </Grid>
          <Grid item xs={12} sm={12} md={5}>
            <VideoContainer />
          </Grid>
        </div>
        <LoginDialog />
        <RegisterDialog />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
