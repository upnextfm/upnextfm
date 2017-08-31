import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { playlistAppend } from 'actions/playlistActions';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import AddIcon from 'material-ui-icons/Add';
import SearchIcon from 'material-ui-icons/Search';
import PlaylistItem from 'components/Video/PlaylistItem';

class PlaylistContainer extends React.Component {
  static propTypes = {
    user: PropTypes.object
  };

  constructor(props) {
    super(props);
    this.state = {
      permalink:   '',
      isSearching: false
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
    const permalink = e.target.value;
    if (permalink.length > 3) {
      this.setState({ permalink, isSearching: permalink.indexOf('http') === -1 });
    } else {
      this.setState({ permalink, isSearching: false });
    }
  };

  handleKeyDown = (e) => {
    if (e.keyCode === 13) {
      this.handleClickPlay();
    }
  };

  renderInput() {
    const iconStyles = {
      width:  36,
      height: 36
    };

    return (
      <div className="up-room-playlist__input">
        <input
          id="up-media-url"
          placeholder="Media link or search term..."
          value={this.state.permalink}
          onChange={this.handleChange}
          onKeyDown={this.handleKeyDown}
          ref={(ref) => { this.inputRef = ref; }}
        />
        <IconButton onClick={this.handleClickAppend}>
          {this.state.isSearching ? (
            <SearchIcon title="Search" style={iconStyles} />
          ) : (
            <AddIcon title="Append" style={iconStyles} />
          )}
        </IconButton>
      </div>
    );
  }

  renderList() {
    const { current, videos, user } = this.props;
    const canDelete = user.roles.indexOf('ROLE_ADMIN') !== -1;

    return (
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
    );
  }

  render() {
    return (
      <div className="up-room-playlist">
        {this.renderInput()}
        {this.renderList()}
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
