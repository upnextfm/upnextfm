import React from 'react';
import { LinearProgress } from 'material-ui/Progress';
import { videoEventDispatcher } from 'utils/events';

export default class Progress extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      time:     0,
      duration: 0
    };
  }

  componentDidMount() {
    videoEventDispatcher.on('time', (e) => {
      this.setState({ time: e.value });
    });
    videoEventDispatcher.on('duration', (e) => {
      this.setState({ duration: e.value });
    });
  }

  render() {
    const { time, duration } = this.state;
    const percent = Math.floor((time / duration) * 100);

    return (
      <div className="up-video-progress-container">
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
