import React from 'react';
import AppBar from 'material-ui/AppBar';
import Toolbar from 'material-ui/Toolbar';
import IconButton from 'material-ui/IconButton';
import ArrowLeft from 'material-ui-icons/KeyboardArrowLeft';
import { animateScrollLeft } from 'utils/animate';

export default class VideoNav extends React.PureComponent {

  handleClickScroll = () => {
    animateScrollLeft(document.body, 0, 50);
  };

  render() {
    return (
      <AppBar position="static" color="default" className="up-nav--video">
        <Toolbar className="up-nav">
          <IconButton onClick={this.handleClickScroll}>
            <ArrowLeft />
          </IconButton>
          <IconButton className="up-badge" onClick={this.handleClickScroll}>
            <div className="up-badge">{4}</div>
          </IconButton>
        </Toolbar>
      </AppBar>
    );
  }
}
