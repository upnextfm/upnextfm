import React from 'react';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { playlistAppend } from 'actions/playlistActions';
import List, { ListItem } from 'material-ui/List';
import Button from 'material-ui/Button';
import PlaylistItem from 'components/Video/PlaylistItem';

class PlaylistContainer extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      permalink: ''
    };
  }

  handleClickPlay = () => {
    this.props.dispatch(playlistAppend(this.inputRef.value));
    this.setState({ permalink: '' });
  };

  handleChange = (e) => {
    this.setState({ permalink: e.target.value });
  };

  render() {
    const { current, videos } = this.props;
    const { permalink } = this.state;

    return (
      <Scrollbars>
        <div className="up-room-playlist up-paper-container">
          <label htmlFor="up-media-url">Media URL: </label>
          <input
            id="up-media-url"
            value={permalink}
            onChange={this.handleChange}
            style={{ color: 'black', width: '75%' }}
            ref={(ref) => { this.inputRef = ref; }}
          />
          <Button onClick={this.handleClickPlay}>
            Append
          </Button>
          <div>
            <List className="up-room-playlist__items">
              {!current.codename ? null : (
                <ListItem key={current.codename} button>
                  <PlaylistItem video={current} isCurrent />
                </ListItem>
              )}
              {videos.map((video) => {
                return (
                  <ListItem key={video.codename} button>
                    <PlaylistItem video={video} />
                  </ListItem>
                );
              })}
            </List>
          </div>
        </div>
      </Scrollbars>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
