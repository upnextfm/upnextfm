import React from 'react';
import Paper from 'material-ui/Paper';

class PlaylistContainer extends React.Component {
  render() {
    return (
      <div className="up-room__playlist">
        <Paper elevation={4} className="up-room__paper_container">
          <div><p>PLAYLIST GOES HERE</p></div>
        </Paper>
      </div>
    );
  }
}

export default PlaylistContainer;
