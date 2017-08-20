import React from 'react';
import { connect } from 'react-redux';
import { LinearProgress } from 'material-ui/Progress';

class Progress extends React.Component {

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
  return Object.assign({}, state.video);
}

export default connect(mapStateToProps)(Progress);
