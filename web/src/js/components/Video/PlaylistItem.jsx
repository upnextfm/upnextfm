import React from 'react';
import PropTypes from 'prop-types';
import ClearIcon from 'material-ui-icons/Clear';
import { formatSeconds } from 'utils/media';

export default class Component extends React.Component {
  static propTypes = {
    video:          PropTypes.object,
    canDelete:      PropTypes.bool,
    isCurrent:      PropTypes.bool,
    onClickControl: PropTypes.func
  };

  static defaultProps = {
    canDelete:      false,
    isCurrent:      false,
    onClickControl: () => {}
  };

  constructor(props) {
    super(props);
    this.state = {
      showControls: false
    };
  }

  handleClickControl = (e) => {
    const action = e.currentTarget.getAttribute('data-action');
    this.props.onClickControl(action, this.props.video);
  };

  handleMouseEnter = () => {
    this.setState({ showControls: true });
  };

  handleMouseLeave = () => {
    this.setState({ showControls: false });
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
    if (!this.state.showControls) {
      return null;
    }

    const { canDelete } = this.props;
    const controlStyles = {
      width:  16,
      height: 16
    };

    return (
      <div className="up-room-playlist__item__controls">
        {!canDelete ? null : (
          <ClearIcon
            data-action="delete"
            style={controlStyles}
            onClick={this.handleClickControl}
          />
        )}
      </div>
    );
  }

  render() {
    const { video } = this.props;

    return (
      <div
        className="up-room-playlist__item"
        onMouseEnter={this.handleMouseEnter}
        onMouseLeave={this.handleMouseLeave}
        data-id={video.id}
      >
        {this.renderThumb()}
        {this.renderMeta()}
        {this.renderControls()}
      </div>
    );
  }
}
