import React from 'react';
import PropTypes from 'prop-types';
import VolumeOff from 'material-ui-icons/VolumeOff';
import VolumeUp from 'material-ui-icons/VolumeUp';

const MuteIcon = ({ isMuted, ...props }) => (
  isMuted ? <VolumeOff {...props} /> : <VolumeUp {...props} />
);

MuteIcon.propTypes = {
  isMuted: PropTypes.bool
};

MuteIcon.defaultProps = {
  isMuted: false
};

export default MuteIcon;
