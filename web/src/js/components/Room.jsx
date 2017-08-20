import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { roomJoin } from 'actions/roomActions';
import * as api from 'api';
import Hidden from 'material-ui/Hidden';
import Grid from 'material-ui/Grid';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import ChatSide from 'components/Chat/ChatSide';
import VideoSide from 'components/Video/VideoSide';
import VideoNav from 'components/VideoNav';
import Nav from 'components/Nav';

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
        <Hidden smUp>
          <VideoNav />
        </Hidden>
        <div className="up-room">
          <Grid item xs={12} sm={12} md={7}>
            <ChatSide />
          </Grid>
          <Grid item xs={12} sm={12} md={5}>
            <VideoSide />
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
