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
      <div className="up-room__video-player">
        <YouTube videoId="NegV-ts35cY" opts={opts} className="up-room__yt-player" />
        <div className="up-room__video_buttons">
          <p>BUTTONS</p>
        </div>
      </div>
    );
  }
}

export default VideoPlayer;
