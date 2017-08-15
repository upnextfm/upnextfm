import React from 'react';
import YouTube from 'react-youtube';

class VideoPlayer extends React.Component {
  render() {
    const opts = {
      width:      '100%',
      playerVars: {
        autoplay: 0
      }
    };

    return (
      <YouTube videoId="NegV-ts35cY" opts={opts} className="up-room__video-player" />
    );
  }
}

export default VideoPlayer;
