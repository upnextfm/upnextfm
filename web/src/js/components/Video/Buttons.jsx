import React from 'react';
import { connect } from 'react-redux';
import { videoToggleMute } from 'actions/videoActions';
import IconButton from 'material-ui/IconButton';
import SkipNext from 'material-ui-icons/SkipNext';
import VolumeOff from 'material-ui-icons/VolumeOff';
import VolumeUp from 'material-ui-icons/VolumeUp';

class Buttons extends React.Component {
  handleClickMute = () => {
    this.props.dispatch(videoToggleMute());
  };

  render() {
    const { video } = this.props;

    return (
      <div className="up-room-video__buttons up-paper-container">
        <IconButton title={video.isMuted ? 'Unmute' : 'Mute'} onClick={this.handleClickMute}>
          {video.isMuted ? <VolumeOff /> : <VolumeUp />}
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
