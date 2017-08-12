import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import MenuIcon from 'material-ui-icons/Menu';
import { navToggleDrawer } from 'actions/navActions';
import NavDrawer from 'components/NavDrawer';

class Nav extends React.Component {
  static propTypes = {
    auth:     PropTypes.object,
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

  handleMenuClick = () => {
    this.props.dispatch(navToggleDrawer());
  };

  render() {
    const { auth } = this.props;

    return (
      <AppBar position="static" color="default">
        <Toolbar className="up-nav">
          <IconButton color="contrast" aria-label="Menu" onClick={this.handleMenuClick}>
            <MenuIcon />
          </IconButton>
          <a href="/" className="up-brand">
            <img src="/images/logo-brand.png" alt="Logo" />
          </a>
          {auth.isAuthenticated
          ? (
            <Button className="up-btn-login" color="contrast">
              Logout
            </Button>
          ) : (
            <Button className="up-btn-login" color="contrast">
              Login
            </Button>
          )}
        </Toolbar>
        <NavDrawer />
      </AppBar>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.nav);
}

export default connect(mapStateToProps)(Nav);
