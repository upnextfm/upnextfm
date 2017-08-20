import React from 'react';
import { animateScrollTo } from 'utils/animate';
import Hidden from 'material-ui/Hidden';
import IconButton from 'material-ui/IconButton';
import ArrowUp from 'material-ui-icons/KeyboardArrowUp';
import Player from 'components/Video/Player';
import Buttons from 'components/Video/Buttons';
import Playlist from 'components/Video/Playlist';

class VideoSide extends React.Component {
  handleClickUp = () => {
    animateScrollTo(document.body, 0, 50);
  };

  render() {
    return (
      <div className="up-room-side__video">
        <Player />
        <Buttons />
        <Playlist />
        <Hidden smUp>
          <div className="up-room-panel__mobile">
            <IconButton onClick={this.handleClickUp}>
              <ArrowUp />
            </IconButton>
          </div>
        </Hidden>
      </div>
    );
  }
}

export default VideoSide;
