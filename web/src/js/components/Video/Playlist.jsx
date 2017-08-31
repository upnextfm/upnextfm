import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { playlistAppend } from 'actions/playlistActions';
import List, { ListItem } from 'material-ui/List';
import Button from 'material-ui/Button';
import PlaylistItem from 'components/Video/PlaylistItem';

class PlaylistContainer extends React.Component {
  static propTypes = {
    user: PropTypes.object
  };

  constructor(props) {
    super(props);
    this.state = {
      permalink: ''
    };
  }

  handleClickAppend = () => {
    this.props.dispatch(playlistAppend(this.inputRef.value));
    this.setState({ permalink: '' });
  };

  handleClickControl = (action, video) => {
    console.info(action, video.id);
  };

  handleChange = (e) => {
    this.setState({ permalink: e.target.value });
  };

  handleKeyDown = (e) => {
    if (e.keyCode === 13) {
      this.handleClickPlay();
    }
  };

  render() {
    const { current, videos, user } = this.props;
    const { permalink } = this.state;
    const canDelete = user.roles.indexOf('ROLE_ADMIN') !== -1;

    return (
      <div className="up-room-playlist">
        <div className="up-room-playlist__input">
          <label htmlFor="up-media-url">URL</label>
          <input
            id="up-media-url"
            value={permalink}
            onChange={this.handleChange}
            onKeyDown={this.handleKeyDown}
            ref={(ref) => { this.inputRef = ref; }}
          />
          <Button onClick={this.handleClickAppend}>
            Append
          </Button>
        </div>
        <Scrollbars className="up-room-playlist__items-container">
          <List className="up-room-playlist__items">
            {!current.codename ? null : (
              <ListItem key={current.codename} button>
                <PlaylistItem
                  video={current}
                  canDelete={canDelete}
                  onClickControl={this.handleClickControl}
                  isCurrent
                />
              </ListItem>
            )}
            {videos.map((video) => {
              return (
                <ListItem key={video.codename} button>
                  <PlaylistItem
                    video={video}
                    canDelete={canDelete}
                    onClickControl={this.handleClickControl}
                  />
                </ListItem>
              );
            })}
          </List>
        </Scrollbars>
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
