import React from 'react';
import { connect } from 'react-redux';
import { playerTogglePlay, playerToggleMute } from 'actions/playerActions';
import IconButton from 'material-ui/IconButton';
import SkipNext from 'material-ui-icons/SkipNext';
import MuteIcon from 'components/Icons/MuteIcon';
import PlayIcon from 'components/Icons/PlayIcon';

class Buttons extends React.Component {
  handleClickMute = () => {
    this.props.dispatch(playerToggleMute());
  };

  handleClickPlay = () => {
    this.props.dispatch(playerTogglePlay());
  };

  render() {
    const { player } = this.props;

    return (
      <div className="up-room-video__buttons up-paper-container">
        <IconButton onClick={this.handleClickPlay}>
          <PlayIcon status={player.status} />
        </IconButton>
        <IconButton onClick={this.handleClickMute}>
          <MuteIcon isMuted={player.isMuted} />
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
    player: state.player
  });
}

export default connect(mapStateToProps)(Buttons);
