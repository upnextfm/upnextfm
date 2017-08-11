import React from 'react';
import MenuItem from 'material-ui/MenuItem';
import DropDownMenu from 'material-ui/DropDownMenu';
import { Toolbar, ToolbarGroup, ToolbarSeparator, ToolbarTitle } from 'material-ui/Toolbar';

class Nav extends React.Component {
  render() {
    return (
      <Toolbar className="up-nav">
        <a href="/">
          <img src="/images/logo-brand.png" className="up-brand" />
        </a>
      </Toolbar>
    );
  }
}

export default Nav;
