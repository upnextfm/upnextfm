import React from 'react';
import { connect } from 'react-redux';
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
      <YouTube videoId="MD8flUkymrM" opts={opts} className="up-room__video-player" />
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.video);
}

export default connect(mapStateToProps)(VideoPlayer);
