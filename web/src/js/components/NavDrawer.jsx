import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { ShareButtons, generateShareIcon } from 'react-share';
import { layoutToggleNavDrawer, layoutToggleHelpDialog } from 'actions/layoutActions';
import List, { ListItem, ListItemIcon, ListItemText } from 'material-ui/List';
import Drawer from 'material-ui/Drawer';
import Divider from 'material-ui/Divider';
import StarIcon from 'material-ui-icons/Star';
import CompareArrows from 'material-ui-icons/CompareArrows';
import AccountCircle from 'material-ui-icons/AccountCircle';
import Comment from 'material-ui-icons/Comment';
import Face from 'material-ui-icons/Face';
import Favorite from 'material-ui-icons/Favorite';
import Help from 'material-ui-icons/Help';
import Info from 'material-ui-icons/Info';

class NavDrawer extends React.Component {
  static propTypes = {
    roomName:        PropTypes.string.isRequired,
    user:            PropTypes.object.isRequired,
    layout:          PropTypes.object,
    dispatch:        PropTypes.func,
    onClickLogin:    PropTypes.func,
    onClickRegister: PropTypes.func
  };

  static defaultProps = {
    dispatch:        () => {},
    onClickLogin:    () => {},
    onClickRegister: () => {}
  };

  handleToggle = () => {
    this.props.dispatch(layoutToggleNavDrawer());
  };

  renderList() {
    const { user, onClickLogin, onClickRegister, dispatch } = this.props;

    let authListItems = null;
    if (user.isAuthenticated) {
      authListItems = (
        <div>
          <ListItem onClick={onClickLogin} button>
            <ListItemIcon>
              <CompareArrows />
            </ListItemIcon>
            <ListItemText primary="Logout" />
          </ListItem>
          <ListItem onClick={() => { window.open(`/u/${user.username}`); }} button>
            <ListItemIcon>
              <Face />
            </ListItemIcon>
            <ListItemText primary="Profile" />
          </ListItem>
          <ListItem onClick={() => { window.open(`/u/${user.username}/favorites`); }} button>
            <ListItemIcon>
              <Favorite />
            </ListItemIcon>
            <ListItemText primary="Favorites" />
          </ListItem>
          <ListItem onClick={() => { window.open('/account'); }} button>
            <ListItemIcon>
              <AccountCircle />
            </ListItemIcon>
            <ListItemText primary="Account" />
          </ListItem>
        </div>
      );
    } else {
      authListItems = (
        <div>
          <ListItem onClick={onClickLogin} button>
            <ListItemIcon>
              <CompareArrows />
            </ListItemIcon>
            <ListItemText primary="Login" />
          </ListItem>
          <ListItem onClick={onClickRegister} button>
            <ListItemIcon>
              <StarIcon />
            </ListItemIcon>
            <ListItemText primary="Register" />
          </ListItem>
        </div>
      );
    }

    const roomListItems = (
      <div>
        <ListItem onClick={() => { window.open(`/chat/logs/${this.props.roomName}`); }} button>
          <ListItemIcon>
            <Comment />
          </ListItemIcon>
          <ListItemText primary="Chat Logs" />
        </ListItem>
      </div>
    );

    const aboutListItems = (
      <div>
        <ListItem onClick={() => { dispatch(layoutToggleHelpDialog()); }} button>
          <ListItemIcon>
            <Help />
          </ListItemIcon>
          <ListItemText primary="Help" />
        </ListItem>
        <ListItem onClick={() => { window.open('/about'); }} button>
          <ListItemIcon>
            <Info />
          </ListItemIcon>
          <ListItemText primary="About Us" />
        </ListItem>
      </div>
    );

    return (
      <div className="up-room-drawer">
        <List disablePadding>
          {authListItems}
        </List>
        <Divider />
        <List disablePadding>
          {roomListItems}
        </List>
        <Divider />
        <List disablePadding>
          {aboutListItems}
        </List>
      </div>
    );
  }

  renderControls() {
    const { TwitterShareButton, RedditShareButton } = ShareButtons;
    const TwitterIcon = generateShareIcon('twitter');
    const RedditIcon = generateShareIcon('reddit');

    return (
      <div className="up-drawer__controls">
        <div>
          <RedditShareButton url={document.location.href}>
            <RedditIcon size={32} round />
          </RedditShareButton>
          <TwitterShareButton url={document.location.href}>
            <TwitterIcon size={32} round />
          </TwitterShareButton>
          <p>
            &copy; 2017 upnext.fm &bull; <a href="/terms">Terms of Use</a>
          </p>
        </div>
      </div>
    );
  }

  render() {
    const { layout } = this.props;

    return (
      <Drawer open={layout.isNavDrawerOpen} onRequestClose={this.handleToggle}>
        {this.renderList()}
        {this.renderControls()}
      </Drawer>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, { layout: state.layout });
}

export default connect(mapStateToProps)(NavDrawer);
