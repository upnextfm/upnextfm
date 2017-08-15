import React from 'react';
import { connect } from 'react-redux';
import IconButton from 'material-ui/IconButton';
import SkipNext from 'material-ui-icons/SkipNext';

class VideoButtons extends React.Component {
  render() {
    return (
      <div className="up-room__video-buttons up-paper-container">
        <IconButton title="Vote Skip" aria-label="Vote Skip">
          <SkipNext />
        </IconButton>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(VideoButtons);
