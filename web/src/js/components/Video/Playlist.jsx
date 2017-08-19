import React from 'react';
import { connect } from 'react-redux';
import { playlistPlay } from 'actions/playlistActions';
import Button from 'material-ui/Button';

class PlaylistContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      videoID: 'MD8flUkymrM'
    };
  }

  handleClickPlay = () => {
    this.props.dispatch(playlistPlay(this.state.videoID));
  };

  render() {
    const { videoID } = this.state;

    return (
      <div className="up-room-video__playlist up-paper-container">
        <label htmlFor="up-youtube-id">YouTUBE ID: </label>
        <input
          id="up-youtube-id"
          value={videoID}
          style={{ color: 'black' }}
          onChange={(e) => { this.setState({ videoID: e.target.value }); }}
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
