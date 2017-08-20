import React from 'react';
import PropTypes from 'prop-types';
import PlayArrow from 'material-ui-icons/PlayArrow';
import Pause from 'material-ui-icons/Pause';
import Stop from 'material-ui-icons/Stop';

const PlayIcon = ({ status, ...props }) => {
  let icon;
  switch (status) {
    case -1:
      icon = <Stop {...props} />;
      break;
    case 2:
      icon = <Pause {...props} />;
      break;
    default:
      icon = <PlayArrow {...props} />;
      break;
  }

  return icon;
};

PlayIcon.propTypes = {
  status: PropTypes.number
};

PlayIcon.defaultProps = {
  status: -1
};

export default PlayIcon;
