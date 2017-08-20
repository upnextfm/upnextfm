import React from 'react';
import { connect } from 'react-redux';
import { videoReady } from 'actions/videoActions';
import YouTube from 'react-youtube';

class Player extends React.Component {
  constructor(props) {
    super(props);
    this.player = null;
  }

  componentDidUpdate() {
    if (this.player) {
      if (this.props.video.isMuted) {
        this.player.mute();
      } else {
        this.player.unMute();
      }
    }
  }

  handleReady = (e) => {
    this.player = e.target;
    this.props.dispatch(videoReady());
  };

  render() {
    const { video, playlist } = this.props;
    const opts = {
      width:      '100%',
      playerVars: {
        widget_referrer: document.location.href,
        start:           video.time,
        showinfo:        0,
        controls:        0,
        autoplay:        1,
        rel:             0
      }
    };

    return (
      <YouTube
        opts={opts}
        videoId={playlist.codename}
        onReady={this.handleReady}
        className="up-room-video__player"
      />
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({
    video:    state.video,
    playlist: state.playlist
  });
}

export default connect(mapStateToProps)(Player);
