import React from 'react';
import PropTypes from 'prop-types';

function formatSeconds(seconds) {
  const date = new Date(1970, 0, 1);
  date.setSeconds(seconds);
  return date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, '$1');
}

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
        <img
          src={video.thumbnail}
          className="up-thumbnail"
          alt="Thumbnail"
        />
        <div className="up-room-playlist__item__meta">
          <div className="up-title">
            {!isCurrent ? null : (
              <img src="/images/equalizer.gif" alt="Equalizer" style={{ marginRight: 4 }} />
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
