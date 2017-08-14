import React from 'react';
import PropTypes from 'prop-types';
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
      <div className="up-room__video">
        <Paper elevation = {4} className = "up-room__paper_container">
          <YouTube videoId="NegV-ts35cY" opts={opts} />
          <div className = "up-room__video_buttons"><p>BUTTONS GO HERE</p></div>
        </Paper>
      </div>
    );
  }
}

export default VideoContainer;
