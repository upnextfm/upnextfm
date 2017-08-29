import React from 'react';
import { connect } from 'react-redux';
import { playlistPlay } from 'actions/playlistActions';
import * as types from 'actions/actionTypes';
import Button from 'material-ui/Button';

class PlaylistContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      permalink: ''
    };
  }

  componentWillUpdate(nextProps) {
    if (nextProps.current.permalink !== this.props.current.permalink) {
      this.setState({ permalink: nextProps.current.permalink });
    }
  }

  handleClickPlay = () => {
    const permalink = this.inputRef.value;
    this.props.dispatch(playlistPlay(permalink));
  };

  handleChange = (e) => {
    this.setState({ permalink: e.target.value });
  };

  render() {
    const { permalink } = this.state;

    return (
      <div className="up-room-video__playlist up-paper-container">
        <label htmlFor="up-media-url">Media URL: </label>
        <input
          id="up-media-url"
          value={permalink}
          onChange={this.handleChange}
          style={{ color: 'black', width: '75%' }}
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
