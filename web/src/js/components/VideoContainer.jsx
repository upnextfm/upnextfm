import React from 'react';
import PropTypes from 'prop-types';
import YouTube from 'react-youtube';

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
        <YouTube videoId="BC2dRkm8ATU" opts={opts} />
      </div>
    );
  }
}

export default VideoContainer;
