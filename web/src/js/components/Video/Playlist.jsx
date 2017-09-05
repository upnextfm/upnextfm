import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { Scrollbars } from 'react-custom-scrollbars';
import { playlistAppend } from 'actions/playlistActions';
import { search } from 'actions/searchActions';
import List, { ListItem } from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import AddIcon from 'material-ui-icons/Add';
import SearchIcon from 'material-ui-icons/Search';
import PlaylistItem from 'components/Video/PlaylistItem';
import PlaylistMenu from 'components/Video/PlaylistMenu';

class PlaylistContainer extends React.PureComponent {
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
      dispatch(playlistAppend(value));
    }
  };

  handleCloseMenu = () => {
    this.setState({ menuOpen: false });
  };

  handleClickMenu = (e) => {
    e.preventDefault();
    this.setState({
      menuOpen:   true,
      menuAnchor: e.currentTarget
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
      width:  36,
      height: 36
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
            <SearchIcon title="Search" style={iconStyles} />
          ) : (
            <AddIcon title="Append" style={iconStyles} />
          )}
        </IconButton>

        <div id="up-datboi">
          <img src="/images/datboi.webp" alt="dat boi" />
        </div>
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
            <ListItem key={current.codename}>
              <PlaylistItem
                video={current}
                canDelete={canDelete}
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
                  canDelete={canDelete}
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
    return (
      <div className="up-room-playlist">
        {this.renderInput()}
        {this.renderList()}
        <PlaylistMenu
          anchor={this.state.menuAnchor}
          isOpen={this.state.menuOpen}
          onRequestClose={this.handleCloseMenu}
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.playlist);
}

export default connect(mapStateToProps)(PlaylistContainer);
