import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Hidden from 'material-ui/Hidden';
import AppBar from './Nav/AppBar';
import Toolbar from './Nav/Toolbar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import { animateScrollLeft } from 'utils/animate';
import { playerToggleMute, playerTogglePlay } from 'actions/playerActions';
import { layoutToggleNavDrawer, layoutToggleLoginDialog, layoutToggleRegisterDialog } from 'actions/layoutActions';
import { userLogout } from 'actions/userActions';
import NavDrawer from 'components/NavDrawer';
import MuteIcon from 'components/Icons/MuteIcon';
import PlayIcon from 'components/Icons/PlayIcon';
import Icon from 'components/Icon';

class Nav extends React.PureComponent {
  static propTypes = {
    roomName: PropTypes.string.isRequired,
    user:     PropTypes.object,
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
    if (this.props.user.isAuthenticated) {
      this.props.dispatch(userLogout());
    } else {
      this.props.dispatch(layoutToggleLoginDialog());
    }
  };

  handleClickRegister = () => {
    this.props.dispatch(layoutToggleRegisterDialog());
  };

  handleClickMute = () => {
    this.props.dispatch(playerToggleMute());
  };

  handleClickPlay = () => {
    this.props.dispatch(playerTogglePlay());
  };

  handleClickScroll = () => {
    animateScrollLeft(document.body, document.body.scrollWidth, 50);
  };

  render() {
    const { roomName, user, video } = this.props;

    return (
      <AppBar>
        <Toolbar>
          <IconButton aria-label="Menu" onClick={this.handleClickMenu}>
            <Icon name="menu" />
          </IconButton>
          <a href="/" className="up-brand">
            <img src="/images/logo-brand.png" alt="Logo" />
          </a>
          <Hidden smDown>
            {user.isAuthenticated
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
          <Hidden mdUp>
            <div className="up-nav__video-controls">
              <IconButton onClick={this.handleClickPlay}>
                <PlayIcon status={video.status} />
              </IconButton>
              <IconButton onClick={this.handleClickMute}>
                <MuteIcon isMuted={video.isMuted} />
              </IconButton>
              <IconButton onClick={this.handleClickScroll}>
                <Icon name="arrow_right" />
              </IconButton>
            </div>
          </Hidden>
        </Toolbar>
        <NavDrawer
          user={user}
          roomName={roomName}
          onClickLogin={this.handleClickLogin}
          onClickRegister={this.handleClickRegister}
        />
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
