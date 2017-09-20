import React from 'react';
import PropTypes from 'prop-types';
import Icon from 'components/Icon';

const PlayIcon = ({ status, ...props }) => {
  let icon;
  switch (status) {
    case -1:
      icon = <Icon name="stop" {...props} />;
      break;
    case 2:
      icon = <Icon name="play_arrow" {...props} />;
      break;
    default:
      icon = <Icon name="pause" {...props} />;
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
