import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import Player from 'components/Video/Player';
import Buttons from 'components/Video/Buttons';
import Playlist from 'components/Video/Playlist';
import Progress from 'components/Video/Progress';

class VideoSide extends React.Component {
  static propTypes = {
    user:     PropTypes.object,
    playlist: PropTypes.object
  };

  render() {
    const { user, playlist } = this.props;

    return (
      <div className="up-room-side__video">
        <div className="up-room-video__player-container">
          <Player video={playlist.current} />
          <Progress />
          <Buttons />
        </div>
        <Playlist user={user} />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({
    user:     state.user,
    playlist: state.playlist
  });
}

export default connect(mapStateToProps)(VideoSide);
