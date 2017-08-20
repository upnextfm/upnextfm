import React from 'react';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';
import Badge from 'material-ui/Badge';
import IconButton from 'material-ui/IconButton';
import ArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import { animateScrollLeft } from 'utils/animate';

export default class VideoNav extends React.Component {

  handleClickScroll = () => {
    animateScrollLeft(document.body, 0, 50);
  };

  render() {
    return (
      <AppBar position="static" color="default" className="up-nav up-nav--video">
        <Toolbar>
          <IconButton onClick={this.handleClickScroll}>
            <ArrowLeft />
          </IconButton>
          <IconButton className="up-badge" onClick={this.handleClickScroll}>
            <Badge badgeContent={4} color="primary" title="New Messages" />
          </IconButton>
        </Toolbar>
      </AppBar>
    );
  }
}
