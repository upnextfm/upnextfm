import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { layoutToggleNavDrawer } from 'actions/layoutActions';
import List, { ListItem, ListItemIcon, ListItemText } from 'material-ui/List';
import Drawer from 'material-ui/Drawer';
import Divider from 'material-ui/Divider';
import StarIcon from 'material-ui-icons/Star';
import CompareArrows from 'material-ui-icons/CompareArrows';
import AccountCircle from 'material-ui-icons/AccountCircle';
import Face from 'material-ui-icons/Face';
import Favorite from 'material-ui-icons/Favorite';
import Help from 'material-ui-icons/Help';
import Info from 'material-ui-icons/Info';

class NavDrawer extends React.Component {
  static propTypes = {
    auth:            PropTypes.object.isRequired,
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
    const { auth, layout, onClickLogin, onClickRegister } = this.props;

    let authListItems = null;
    if (auth.isAuthenticated) {
      authListItems = (
        <div>
          <ListItem onClick={onClickLogin} button>
            <ListItemIcon>
              <CompareArrows />
            </ListItemIcon>
            <ListItemText primary="Logout" />
          </ListItem>
          <ListItem button>
            <ListItemIcon>
              <Face />
            </ListItemIcon>
            <ListItemText primary="Profile" />
          </ListItem>
          <ListItem button>
            <ListItemIcon>
              <Favorite />
            </ListItemIcon>
            <ListItemText primary="Favorites" />
          </ListItem>
          <ListItem button>
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

    const aboutListItems = (
      <div>
        <ListItem button>
          <ListItemIcon>
            <Info />
          </ListItemIcon>
          <ListItemText primary="About Us" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <Help />
          </ListItemIcon>
          <ListItemText primary="Help" />
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
          {aboutListItems}
        </List>
      </div>
    );
  }

  render() {
    const { layout } = this.props;

    return (
      <Drawer open={layout.isNavDrawerOpen} onRequestClose={this.handleToggle}>
        {this.renderList()}
      </Drawer>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, { layout: state.layout });
}

export default connect(mapStateToProps)(NavDrawer);
