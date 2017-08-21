import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Hidden from 'material-ui/Hidden';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import MenuIcon from 'material-ui-icons/Menu';
import VolumeOff from 'material-ui-icons/VolumeOff';
import VolumeUp from 'material-ui-icons/VolumeUp';
import ArrowRight from 'material-ui-icons/KeyboardArrowRight';
import PlayArrow from 'material-ui-icons/PlayArrow';
import { animateScrollLeft } from 'utils/animate';
import { layoutToggleNavDrawer } from 'actions/layoutActions';
import { videoToggleMute, videoTogglePlay, videoStatus } from 'actions/videoActions';
import { authToggleLoginDialog, authLogout } from 'actions/authActions';
import { registerToggleDialog } from 'actions/registerActions';
import NavDrawer from 'components/NavDrawer';
import MuteIcon from 'components/Icons/MuteIcon';
import PlayIcon from 'components/Icons/PlayIcon';

class Nav extends React.Component {
  static propTypes = {
    auth:     PropTypes.object,
    layout:   PropTypes.object,
    video:    PropTypes.object,
    dispatch: PropTypes.func
  };

  static defaultProps = {
    dispatch: () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      drawerOpen: false
    };
  }

  handleClickMenu = () => {
    this.props.dispatch(layoutToggleNavDrawer());
  };

  handleClickLogin = () => {
    if (this.props.auth.isAuthenticated) {
      this.props.dispatch(authLogout());
    } else {
      this.props.dispatch(authToggleLoginDialog());
    }
  };

  handleClickRegister = () => {
    this.props.dispatch(registerToggleDialog());
  };

  handleClickMute = () => {
    this.props.dispatch(videoToggleMute());
  };

  handleClickPlay = () => {
    this.props.dispatch(videoTogglePlay());
  };

  handleClickScroll = () => {
    animateScrollLeft(document.body, document.body.scrollWidth, 50);
  };

  render() {
    const { auth, video } = this.props;

    return (
      <AppBar position="static" color="default">
        <Toolbar className="up-nav">
          <IconButton aria-label="Menu" onClick={this.handleClickMenu}>
            <MenuIcon />
          </IconButton>
          <a href="/" className="up-brand">
            <img src="/images/logo-brand.png" alt="Logo" />
          </a>
          <Hidden xsDown>
            {auth.isAuthenticated
            ? (
              <Button className="up-btn-login" onClick={this.handleClickLogin}>
                Logout
              </Button>
            ) : (
              <span className="up-btn-login">
                <Button className="up-btn-login" onClick={this.handleClickRegister}>
                  Register
                </Button>
                <Button className="up-btn-login" onClick={this.handleClickLogin}>
                  Login
                </Button>
              </span>
            )}
          </Hidden>
          <Hidden smUp>
            <div className="up-nav__video-controls">
              <IconButton onClick={this.handleClickPlay}>
                <PlayIcon status={video.status} />
              </IconButton>
              <IconButton onClick={this.handleClickMute}>
                <MuteIcon isMuted={video.isMuted} />
              </IconButton>
              <IconButton onClick={this.handleClickScroll}>
                <ArrowRight />
              </IconButton>
            </div>
          </Hidden>
        </Toolbar>
        <NavDrawer auth={auth} onClickLogin={this.handleClickLogin} onClickRegister={this.handleClickRegister} />
      </AppBar>
    );
  }
}

function mapStateToProps(state) {
  return {
    layout: Object.assign({}, state.layout),
    video:  Object.assign({}, state.video)
  };
}

export default connect(mapStateToProps)(Nav);
