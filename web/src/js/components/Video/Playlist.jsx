import React from 'react';
import { connect } from 'react-redux';
import { playlistPlay } from 'actions/playlistActions';
import * as types from 'actions/actionTypes';
import Button from 'material-ui/Button';

class PlaylistContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      codename: ''
    };
  }

  componentWillUpdate(nextProps) {
    if (nextProps.codename !== this.props.codename) {
      this.setState({ codename: nextProps.codename });
    }
  }

  handleClickPlay = () => {
    const codename = this.inputRef.value;
    this.props.dispatch(playlistPlay(codename, types.PROVIDER_YOUTUBE));
  };

  handleChange = (e) => {
    this.setState({ codename: e.target.value });
  };

  render() {
    const { codename } = this.state;

    return (
      <div className="up-room-video__playlist up-paper-container">
        <label htmlFor="up-youtube-id">YouTUBE ID: </label>
        <input
          id="up-youtube-id"
          value={codename}
          onChange={this.handleChange}
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
