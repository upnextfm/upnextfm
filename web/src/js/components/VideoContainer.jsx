import React from 'react';
import YouTube from 'react-youtube';
import Paper from 'material-ui/Paper';

class VideoContainer extends React.Component {
  render() {
    const opts = {
      width:      '100%',
      playerVars: {
        autoplay: 0
      }
    };

    return (
      <Paper elevation={4} className="up-room__video up-paper-container">
        <YouTube videoId="NegV-ts35cY" opts={opts} className="up-room__yt-player" />
        <div className="up-room__video_buttons">
          <p>BUTTONS GO HERE</p>
        </div>
      </Paper>
    );
  }
}

export default VideoContainer;
