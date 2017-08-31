import React from 'react';
import PropTypes from 'prop-types';
import { formatSeconds } from 'utils/media';

export default class Component extends React.Component {
  static propTypes = {
    video:     PropTypes.object,
    isCurrent: PropTypes.bool
  };

  static defaultProps = {
    isCurrent: false
  };

  render() {
    const { video, isCurrent } = this.props;

    return (
      <div className="up-room-playlist__item">
        <a href={video.permalink} target="_blank">
          <img
            src={video.thumbnail}
            className="up-thumbnail"
            alt="Thumbnail"
          />
        </a>
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
      </div>
    );
  }
}
