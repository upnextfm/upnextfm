import React from 'react';
import { connect } from 'react-redux';
import { videoTogglePlay, videoToggleMute } from 'actions/videoActions';
import IconButton from 'material-ui/IconButton';
import SkipNext from 'material-ui-icons/SkipNext';
import MuteIcon from 'components/Icons/MuteIcon';
import PlayIcon from 'components/Icons/PlayIcon';

class Buttons extends React.Component {
  handleClickMute = () => {
    this.props.dispatch(videoToggleMute());
  };

  handleClickPlay = () => {
    this.props.dispatch(videoTogglePlay());
  };

  render() {
    const { video } = this.props;

    return (
      <div className="up-room-video__buttons up-paper-container">
        <IconButton onClick={this.handleClickPlay}>
          <PlayIcon status={video.status} />
        </IconButton>
        <IconButton onClick={this.handleClickMute}>
          <MuteIcon isMuted={video.isMuted} />
        </IconButton>
        <IconButton title="Vote Skip">
          <SkipNext />
        </IconButton>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({
    video:    state.video,
    playlist: state.playlist
  });
}

export default connect(mapStateToProps)(Buttons);
