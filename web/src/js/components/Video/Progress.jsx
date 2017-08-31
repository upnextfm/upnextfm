import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { connect } from 'react-redux';
import { LinearProgress } from 'material-ui/Progress';
import { videoEventDispatcher } from 'utils/events';

class Progress extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      time: 0
    };
    this.offTime = null;
  }

  componentDidMount() {
    this.offTime = videoEventDispatcher.on('time', (e) => {
      this.setState({ time: e.value });
    });
  }

  componentWillUnmount() {
    this.offTime();
  }

  render() {
    const { className, duration } = this.props;
    const { time } = this.state;
    const percent = Math.floor((time / duration) * 100);

    return (
      <div className={classNames('up-video-progress-container', className)}>
        <LinearProgress
          color="primary"
          mode="determinate"
          value={isNaN(percent) ? 0 : percent}
          className="up-video-progress"
        />
      </div>
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.player);
}

export default connect(mapStateToProps)(Progress);
