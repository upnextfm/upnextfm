import React from 'react';
import { connect } from 'react-redux';
import { playlistPlay } from 'actions/playlistActions';
import * as types from 'actions/actionTypes';
import Button from 'material-ui/Button';

class PlaylistContainer extends React.Component {
  handleClickPlay = () => {
    const codename = this.inputRef.value;
    this.props.dispatch(playlistPlay(codename, types.PROVIDER_YOUTUBE));
  };

  render() {
    const { codename } = this.props;

    return (
      <div className="up-room-video__playlist up-paper-container">
        <label htmlFor="up-youtube-id">YouTUBE ID: </label>
        <input
          id="up-youtube-id"
          defaultValue={codename}
          style={{ color: 'black' }}
          ref={(ref) => { this.inputRef = ref; }}
        />
        <Button onClick={this.handleClickPlay}>Play</Button>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
