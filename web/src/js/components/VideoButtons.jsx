import React from 'react';
import { connect } from 'react-redux';

class VideoButtons extends React.Component {
  render() {
    return (
      <div className="up-room__video-buttons up-paper-container">
        BUTTONS
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(VideoButtons);
