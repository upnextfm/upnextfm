import React from 'react';
import { connect } from 'react-redux';
import { videoReady, videoTime } from 'actions/videoActions';
import YouTube from 'react-youtube';

class Player extends React.Component {
  constructor(props) {
    super(props);
    this.interval = null;
    this.player   = null;
  }

  componentDidUpdate() {
    if (this.player) {
      if (this.shouldSeekTo()) {
        this.player.seekTo(this.props.video.time);
      }
      if (this.props.video.isMuted) {
        this.player.mute();
      } else {
        this.player.unMute();
      }
    }
  }

  componentWillUnmount() {
    clearInterval(this.interval);
  }

  shouldSeekTo = () => {
    const diff = this.props.video.time - this.player.getCurrentTime();
    return diff > 5;
  };

  handleInterval = () => {
    const time = parseInt(this.player.getCurrentTime(), 10);
    this.props.dispatch(videoTime(time));
  };

  handleReady = (e) => {
    this.player = e.target;
    this.props.dispatch(videoReady());
    this.player.seekTo(this.props.video.time);
    setInterval(this.handleInterval, 1000);
  };

  render() {
    const { playlist } = this.props;
    const opts = {
      width:      '100%',
      playerVars: {
        widget_referrer: document.location.href,
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
