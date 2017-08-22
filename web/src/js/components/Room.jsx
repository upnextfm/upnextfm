import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { roomJoin } from 'actions/roomActions';
import { layoutWindowFocused } from 'actions/layoutActions';
import { domOnWindowBlur } from 'utils/dom';
import * as api from 'api';
import Hidden from 'material-ui/Hidden';
import Grid from 'material-ui/Grid';
import LoginDialog from 'components/Dialogs/LoginDialog';
import RegisterDialog from 'components/Dialogs/RegisterDialog';
import ChatSide from 'components/Chat/ChatSide';
import VideoSide from 'components/Video/VideoSide';
import VideoNav from 'components/VideoNav';
import Nav from 'components/Nav';
import Progress from 'components/Video/Progress';

class Room extends React.Component {
  static propTypes = {
    name:      PropTypes.string.isRequired,
    socketURI: PropTypes.string.isRequired,
    auth:      PropTypes.object.isRequired,
    layout:    PropTypes.object.isRequired,
    dispatch:  PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    this.title = document.title;
  }

  componentDidMount() {
    api.socket.connect(this.props.socketURI)
      .then(() => {
        this.props.dispatch(roomJoin(this.props.name));
      });
    domOnWindowBlur((status) => {
      this.props.dispatch(layoutWindowFocused(status));
    });
  }

  componentDidUpdate() {
    if (!this.props.layout.isWindowFocused && this.props.room.numNewMessages > 0) {
      document.title = `*${this.title}`;
    } else {
      document.title = this.title;
    }
  }

  render() {
    const { auth, layout } = this.props;

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
            <ChatSide />
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
  return Object.assign({}, state);
}

export default connect(mapStateToProps)(Room);
