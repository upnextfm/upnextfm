import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Player from 'components/Video/Player';
import Buttons from 'components/Video/Buttons';
import Playlist from 'components/Video/Playlist';
import Progress from 'components/Video/Progress';

class VideoSide extends React.Component {
  static propTypes = {
    playlist: PropTypes.object.isRequired
  };

  render() {
    const { playlist } = this.props;

    return (
      <div className="up-room-side__video">
        <Player video={playlist.current} />
        <Progress />
        <Buttons />
        <Playlist />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({
    playlist: state.playlist
  });
}

export default connect(mapStateToProps)(VideoSide);
