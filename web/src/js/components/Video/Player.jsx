import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import * as actions from 'actions/playerActions';
import eventDispatcher from 'utils/events';
import YouTube from 'react-youtube';

class Player extends React.Component {
  static propTypes = {
    video:  PropTypes.object,
    player: PropTypes.object
  };

  constructor(props) {
    super(props);
    this.interval = null;
    this.provider = null;
  }

  shouldComponentUpdate(nextProps) {
    // return nextProps.player.time === this.props.player.time;
    return true;
  }

  componentDidUpdate(prevProps) {
    if (this.provider) {
      if (this.shouldSeekTo()) {
        this.provider.seekTo(this.props.player.time);
      }
      if (prevProps.player.status !== this.props.player.status) {
        switch (this.props.player.status) { // eslint-disable-line
          case 1:
            this.provider.playVideo();
            break;
          case 2:
            this.provider.pauseVideo();
            break;
        }
      }
      if (this.props.player.isMuted) {
        this.provider.mute();
      } else {
        this.provider.unMute();
      }
    }
  }

  componentWillUnmount() {
    clearInterval(this.interval);
  }

  shouldSeekTo = () => {
    const diff = this.props.player.time - this.provider.getCurrentTime();
    return diff > 5;
  };

  handleInterval = () => {
    eventDispatcher.trigger('player.time', parseInt(this.provider.getCurrentTime(), 10));
  };

  handleReady = (e) => {
    this.provider = e.target;

    const duration = parseInt(this.provider.getDuration(), 10);
    this.props.dispatch(actions.playerReady(duration));
    setInterval(this.handleInterval, 1000);
  };

  handleStateChange = () => {
    const status = this.provider.getPlayerState();
    this.props.dispatch(actions.playerStatus(status));
  };

  renderProviderYoutube() {
    const opts    = {
      width:      '100%',
      playerVars: {
        widget_referrer: document.location.href,
        start:           this.props.video.start,
        showinfo:        0,
        controls:        0,
        autoplay:        1,
        rel:             0
      }
    };

    return (
      <YouTube
        opts={opts}
        videoId={this.props.video.codename}
        onReady={this.handleReady}
        onStateChange={this.handleStateChange}
        className="up-room-video__player up-room-video__player--youtube"
      />
    );
  }

  render() {
    const { video } = this.props;
    if (!video.codename) {
      return (
        <div className="up-room-video__container" />
      );
    }

    let player;
    switch (video.provider) {
      case 'youtube':
        player = this.renderProviderYoutube();
        break;
      default:
        console.error(`Player provider "${video.provider}" invalid.`);
        break;
    }

    return (
      <div className="up-room-video__container">
        {player}
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({
    player: state.player
  });
}

export default connect(mapStateToProps)(Player);
