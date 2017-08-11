import React from 'react';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';

class Nav extends React.Component {
  render() {
    return (
      <AppBar position="static" color="default">
        <Toolbar className="up-nav">
          <a href="/">
            <img src="/images/logo-brand.png" className="up-brand" />
          </a>
        </Toolbar>
      </AppBar>
    );
  }
}

export default Nav;
