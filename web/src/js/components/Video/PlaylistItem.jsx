import React from 'react';
import PropTypes from 'prop-types';
import MoreIcon from 'material-ui-icons/MoreVert';
import { formatSeconds } from 'utils/media';

export default class PlaylistItem extends React.Component {
  static propTypes = {
    video:       PropTypes.object,
    canDelete:   PropTypes.bool,
    isCurrent:   PropTypes.bool,
    onClickMenu: PropTypes.func
  };

  static defaultProps = {
    canDelete:   false,
    isCurrent:   false,
    onClickMenu: () => {}
  };

  renderThumb() {
    const { video } = this.props;

    return (
      <a href={video.permalink} target="_blank">
        <img
          src={video.thumbnail}
          className="up-thumbnail"
          alt="Thumbnail"
        />
      </a>
    );
  }

  renderMeta() {
    const { video, isCurrent } = this.props;

    return (
      <div className="up-room-playlist__item__meta">
        <div className="up-title">
          {!isCurrent ? null : (
            <img src="/images/equalizer.gif" alt="Equalizer" style={{ height: 12, marginRight: 4 }} />
          )}
          {video.title}
        </div>
        <div className="up-info">
          {formatSeconds(video.seconds)}
        </div>
      </div>
    );
  }

  renderControls() {
    return (
      <div className="up-room-playlist__item__controls">
        <MoreIcon onClick={this.props.onClickMenu} />
      </div>
    );
  }

  render() {
    return (
      <div
        className="up-room-playlist__item"
        onMouseEnter={this.handleMouseEnter}
        onMouseLeave={this.handleMouseLeave}
      >
        {this.renderThumb()}
        {this.renderMeta()}
        {this.renderControls()}
      </div>
    );
  }
}
