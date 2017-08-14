import React from 'react';
import PropTypes from 'prop-types';
import Paper from 'material-ui/Paper';
class PlaylistContainer extends React.Component {
  render() {
    const opts = {
      width:      '100%',
    };

    return (
      <Paper elevation = {4} className = "up-room__paper_container up-room__playlist">
        <div><p>PLAYLIST GOES HERE</p></div>
      </Paper>
    );
  }
}

export default PlaylistContainer;
