import React from 'react';
import Player from 'components/Video/Player';
import Buttons from 'components/Video/Buttons';
import Playlist from 'components/Video/Playlist';

class VideoSide extends React.Component {
  render() {
    return (
      <div className="up-room-side__video">
        <Player />
        <Buttons />
        <Playlist />
      </div>
    );
  }
}

export default VideoSide;
