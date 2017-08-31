import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Hidden from 'material-ui/Hidden';
import Grid from 'material-ui/Grid';
import { settingsSocket } from 'actions/settingsActions';
import { userUsername } from 'actions/userActions';
import { layoutToggleLoginDialog, layoutToggleRegisterDialog, layoutToggleHelpDialog, layoutErrorMessage } from 'actions/layoutActions';
import Progress from 'components/Video/Progress';
import HelpDialog from 'components/Dialogs/HelpDialog';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import ChatSide from 'components/Chat/ChatSide';
import VideoSide from 'components/Video/VideoSide';
import VideoNav from 'components/VideoNav';
import Nav from 'components/Nav';
import ErrorSnackbar from 'components/ErrorSnackbar';

class Room extends React.Component {
  static propTypes = {
    roomName:       PropTypes.string.isRequired,
    socketSettings: PropTypes.object.isRequired,
    username:       PropTypes.string,
    user:           PropTypes.object,
    layout:         PropTypes.object
  };

  constructor(props) {
    super(props);
    props.dispatch(settingsSocket(props.socketSettings));
    props.dispatch(userUsername(props.username));
  }

  handleCloseErrorSnackbar = () => {
    this.props.dispatch(layoutErrorMessage(''));
  };

  render() {
    const { roomName, user, layout, dispatch } = this.props;

    return (
      <div>
        <Nav user={user} roomName={roomName} />
        <Hidden smUp>
          <VideoNav />
        </Hidden>
        <Hidden smUp>
          <Progress className="up-video-progress--thin" />
        </Hidden>
        <div className="up-room">
          <Grid item xs={12} md={layout.colsChatSide}>
            <ChatSide roomName={roomName} />
          </Grid>
          <Grid item xs={12} md={layout.colsVideoSide}>
            <VideoSide />
          </Grid>
        </div>
        <ErrorSnackbar
          errorMessage={layout.errorMessage}
          errorDuration={layout.errorDuration}
          onClose={this.handleCloseErrorSnackbar}
        />
        <HelpDialog
          isOpen={layout.isHelpDialogOpen}
          onClose={() => { dispatch(layoutToggleHelpDialog()); }}
        />
        <LoginDialog
          isOpen={layout.isLoginDialogOpen}
          onClose={() => { dispatch(layoutToggleLoginDialog()); }}
        />
        <RegisterDialog
          isOpen={layout.isRegisterDialogOpen}
          onClose={() => { dispatch(layoutToggleRegisterDialog()); }}
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {
    user:   Object.assign({}, state.user),
    layout: Object.assign({}, state.layout)
  };
}

export default connect(mapStateToProps)(Room);
