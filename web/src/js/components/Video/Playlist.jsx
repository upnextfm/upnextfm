import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import * as actions from 'actions/playlistActions';
import { search } from 'actions/searchActions';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import { formatSeconds } from 'utils/media';
import PlaylistItem from 'components/Video/PlaylistItem';
import PlaylistMenu from 'components/Video/PlaylistMenu';
import Icon from 'components/Icon';

class Playlist extends React.PureComponent {
  static propTypes = {
    user: PropTypes.object
  };

  constructor(props) {
    super(props);
    this.state = {
      permalink:   '',
      isSearching: false,
      menuAnchor:  undefined,
      menuOpen:    false
    };
  }

  datboi() {
    const el = $('#up-datboi');
    el.fadeIn().animate({
      right: '100%'
    }, 3000, () => {
      el.hide().css('right', '-400px');
    });
  }

  handleClickAppend = () => {
    const value = this.inputRef.value;
    this.setState({ permalink: '' });

    if (value.toLowerCase().indexOf('o shit waddup') === 0) {
      this.datboi();
      return;
    }

    const dispatch = this.props.dispatch;
    if (this.state.isSearching) {
      dispatch(search(value));
      this.setState({ isSearching: false });
    } else {
      dispatch(actions.playlistAppend(value));
    }
  };

  handleCloseMenu = () => {
    this.setState({ menuOpen: false });
  };

  handleClickMenuAction = (e, action, videoID) => {
    const dispatch = this.props.dispatch;
    switch (action) {
      case 'upvote':
        dispatch(actions.playlistUpvote(videoID));
        break;
      case 'remove':
        dispatch(actions.playlistRemove(videoID));
        break;
      case 'playNext':
        dispatch(actions.playlistPlayNext(videoID));
        break;
      default:
        console.error(`Unknown playlist action "${action}".`);
        break;
    }

    this.handleCloseMenu();
  };

  handleClickMenu = (e, videoID) => {
    e.preventDefault();
    this.setState({
      menuOpen:    true,
      menuAnchor:  e.currentTarget,
      menuVideoID: videoID
    });
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
      this.handleClickAppend();
    }
  };

  renderInput() {
    const iconStyles = {
      fontSize: 36
    };

    return (
      <div className="up-room-playlist__input" onClick={() => { this.inputRef.focus(); }}>
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
            <Icon name="search" title="Search" style={iconStyles} />
          ) : (
            <Icon name="add" title="Append" style={iconStyles} />
          )}
        </IconButton>

        <div id="up-datboi">
          <img src="/images/datboi.webp" alt="dat boi" />
        </div>
      </div>
    );
  }

  renderDetails() {
    const { current, videos } = this.props;

    let numVideos = videos.length;
    let seconds   = 0;
    for (let i = 0; i < numVideos; i += 1) {
      seconds += videos[i].seconds;
    }
    if (current.id !== undefined) {
      numVideos += 1;
      seconds += current.seconds;
    }

    return (
      <div className="up-room-playlist__details">
        <span>{formatSeconds(seconds)}</span>
        <span>&middot;</span>
        <span>{numVideos} {numVideos === 1 ? 'video' : 'videos'}</span>
        <span style={{ marginLeft: 'auto', paddingRight: 0 }}><Icon name="more_vert" className="up-icon" /></span>
      </div>
    );
  }

  renderList() {
    const { current, videos } = this.props;

    return (
      <Scrollbars className="up-room-playlist__items-container">
        <List className="up-room-playlist__items">
          {!current.codename ? null : (
            <ListItem key={current.codename}>
              <PlaylistItem
                video={current}
                onClickMenu={this.handleClickMenu}
                isCurrent
              />
            </ListItem>
          )}
          {videos.map((video) => {
            return (
              <ListItem key={video.codename}>
                <PlaylistItem
                  video={video}
                  onClickMenu={this.handleClickMenu}
                />
              </ListItem>
            );
          })}
        </List>
      </Scrollbars>
    );
  }

  render() {
    const { user } = this.props;

    return (
      <div className="up-room-playlist">
        {this.renderInput()}
        {this.renderDetails()}
        {this.renderList()}
        <PlaylistMenu
          videoID={this.state.menuVideoID}
          anchor={this.state.menuAnchor}
          isOpen={this.state.menuOpen}
          permissions={{
            remove:   user.roles.indexOf('ROLE_ADMIN') !== -1,
            playNext: user.roles.indexOf('ROLE_ADMIN') !== -1
          }}
          onClick={this.handleClickMenuAction}
          onRequestClose={this.handleCloseMenu}
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(Playlist);
