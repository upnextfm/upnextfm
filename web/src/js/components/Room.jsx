import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Hidden from 'material-ui/Hidden';
import Grid from 'material-ui/Grid';
import Progress from 'components/Video/Progress';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import ChatSide from 'components/Chat/ChatSide';
import VideoSide from 'components/Video/VideoSide';
import VideoNav from 'components/VideoNav';
import Nav from 'components/Nav';

class Room extends React.Component {
  static propTypes = {
    roomName:  PropTypes.string.isRequired,
    socketURI: PropTypes.string.isRequired,
    auth:      PropTypes.object.isRequired,
    layout:    PropTypes.object.isRequired
  };

  render() {
    const { roomName, socketURI, auth, layout } = this.props;

    return (
      <div>
        <Nav auth={auth} />
        <Hidden smUp>
          <VideoNav />
        </Hidden>
        <Hidden smUp>
          <Progress />
        </Hidden>
        <div className="up-room">
          <Grid item xs={12} sm={12} md={layout.colsChatSide}>
            <ChatSide roomName={roomName} socketURI={socketURI} />
          </Grid>
          <Grid item xs={12} sm={12} md={layout.colsVideoSide}>
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
  return {
    auth:   Object.assign({}, state.auth),
    layout: Object.assign({}, state.layout)
  };
}

export default connect(mapStateToProps)(Room);
