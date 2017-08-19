import React from 'react';
import { connect } from 'react-redux';

class PlaylistContainer extends React.Component {
  render() {
    return (
      <div className="up-room-video__playlist up-paper-container">
        PLAYLIST
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
