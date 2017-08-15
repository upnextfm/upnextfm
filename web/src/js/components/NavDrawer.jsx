import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Drawer from 'material-ui/Drawer';
import List, { ListItem, ListItemIcon, ListItemText } from 'material-ui/List';
import Divider from 'material-ui/Divider';
import InboxIcon from 'material-ui-icons/Inbox';
import DraftsIcon from 'material-ui-icons/Drafts';
import StarIcon from 'material-ui-icons/Star';
import CompareArrows from 'material-ui-icons/CompareArrows';
import SendIcon from 'material-ui-icons/Send';
import MailIcon from 'material-ui-icons/Mail';
import DeleteIcon from 'material-ui-icons/Delete';
import ReportIcon from 'material-ui-icons/Report';
import { navToggleDrawer } from 'actions/navActions';

class NavDrawer extends React.Component {
  static propTypes = {
    auth:            PropTypes.object.isRequired,
    isDrawerOpen:    PropTypes.bool,
    dispatch:        PropTypes.func,
    onClickLogin:    PropTypes.func,
    onClickRegister: PropTypes.func
  };

  static defaultProps = {
    isDrawerOpen: false,
    dispatch:        () => {},
    onClickLogin:    () => {},
    onClickRegister: () => {}
  };

  handleToggle = () => {
    this.props.dispatch(navToggleDrawer());
  };

  renderList() {
    const { auth, onClickLogin, onClickRegister } = this.props;

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

    const mailFolderListItems = (
      <div>
        <ListItem button>
          <ListItemIcon>
            <InboxIcon />
          </ListItemIcon>
          <ListItemText primary="Inbox" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <StarIcon />
          </ListItemIcon>
          <ListItemText primary="Starred" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <SendIcon />
          </ListItemIcon>
          <ListItemText primary="Send mail" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <DraftsIcon />
          </ListItemIcon>
          <ListItemText primary="Drafts" />
        </ListItem>
      </div>
    );

    const otherMailFolderListItems = (
      <div>
        <ListItem button>
          <ListItemIcon>
            <MailIcon />
          </ListItemIcon>
          <ListItemText primary="All mail" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <DeleteIcon />
          </ListItemIcon>
          <ListItemText primary="Trash" />
        </ListItem>
        <ListItem button>
          <ListItemIcon>
            <ReportIcon />
          </ListItemIcon>
          <ListItemText primary="Spam" />
        </ListItem>
      </div>
    );

    return (
      <div className="up-drawer">
        <List disablePadding>
          {authListItems}
        </List>
        <Divider />
        <List disablePadding>
          {mailFolderListItems}
        </List>
        <Divider />
        <List disablePadding>
          {otherMailFolderListItems}
        </List>
      </div>
    );
  }

  render() {
    const { isDrawerOpen } = this.props;

    return (
      <Drawer open={isDrawerOpen} onRequestClose={this.handleToggle}>
        {this.renderList()}
      </Drawer>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.nav);
}

export default connect(mapStateToProps)(NavDrawer);
