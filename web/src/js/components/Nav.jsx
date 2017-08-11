import React from 'react';
import PropTypes from 'prop-types';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';
import Button from 'material-ui/Button';
import IconButton from 'material-ui/IconButton';
import MenuIcon from 'material-ui-icons/Menu';

export default class Nav extends React.Component {
  static propTypes = {
    auth: PropTypes.object
  };

  render() {
    const { auth } = this.props;

    return (
      <AppBar position="static" color="default">
        <Toolbar className="up-nav">
          <IconButton color="contrast" aria-label="Menu">
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
      </AppBar>
    );
  }
}

