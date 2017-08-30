import React from 'react';
import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { LinearProgress } from 'material-ui/Progress';

class Progress extends React.Component {
  static propTypes = {
    time:     PropTypes.number,
    duration: PropTypes.number
  };

  static defaultProps = {
    time:     0,
    duration: 0
  };

  render() {
    const { time, duration } = this.props;
    const percent = Math.floor((time / duration) * 100);

    return (
      <LinearProgress
        color="primary"
        mode="determinate"
        value={percent}
        className="up-video-progress"
      />
    );
  }
}

function mapStateToProps(state) {
  return Object.assign({}, state.player);
}

export default connect(mapStateToProps)(Progress);
